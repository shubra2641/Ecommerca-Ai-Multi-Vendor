<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::orderBy('is_default', 'desc')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.languages.index', compact('languages'));
    }

    public function create()
    {
        return view('admin.languages.create');
    }

    public function store(Request $request, HtmlSanitizer $sanitizer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:2|unique:languages,code',
            'flag' => 'nullable|string|max:10',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // sanitize inputs
        $clean = [];
        foreach (['name', 'code', 'flag'] as $k) {
            if ($request->has($k) && is_string($request->input($k))) {
                $clean[$k] = $sanitizer->clean($request->input($k));
            }
        }

        \DB::transaction(function () use ($request, $clean): void {
            // If setting as default, remove default from others
            if ($request->is_default) {
                Language::where('is_default', true)->update(['is_default' => false]);
            }

            $payload = array_merge($request->all(), $clean);
            $language = Language::create($payload);

            // Create translation file
            $this->createTranslationFile($language->code);
        });

        return redirect()->route('admin.languages.index')
            ->with('success', __('Language created successfully'));
    }

    public function edit(Language $language)
    {
        return view('admin.languages.edit', compact('language'));
    }

    public function update(Request $request, Language $language, HtmlSanitizer $sanitizer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:2|unique:languages,code,'.$language->id,
            'flag' => 'nullable|string|max:10',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // sanitize inputs
        $clean = [];
        foreach (['name', 'code', 'flag'] as $k) {
            if ($request->has($k) && is_string($request->input($k))) {
                $clean[$k] = $sanitizer->clean($request->input($k));
            }
        }

        \DB::transaction(function () use ($request, $language, $clean): void {
            // If setting as default, remove default from others
            if ($request->is_default && ! $language->is_default) {
                Language::where('is_default', true)->update(['is_default' => false]);
            }

            $language->update(array_merge($request->all(), $clean));
        });

        return redirect()->route('admin.languages.index')
            ->with('success', __('Language updated successfully'));
    }

    public function destroy(Language $language)
    {
        if ($language->is_default) {
            return redirect()->route('admin.languages.index')
                ->with('error', __('Cannot delete default language'));
        }

        // Delete translation file
        $path = resource_path("lang/{$language->code}.json");
        if (File::exists($path)) {
            File::delete($path);
        }

        $language->delete();

        return redirect()->route('admin.languages.index')
            ->with('success', __('Language deleted successfully'));
    }

    public function translations(Language $language)
    {
        $translations = $this->getTranslations($language->code);

        return view('admin.languages.translations', compact('language', 'translations'));
    }

    public function updateTranslations(Request $request, Language $language, HtmlSanitizer $sanitizer)
    {
        $translations = $request->input('translations', []);

        // Validate that translations is an array
        if (! is_array($translations)) {
            return redirect()->back()->with('error', __('Invalid translation data'));
        }

        // Sanitize all translation values to avoid HTML/JS injection
        array_walk_recursive($translations, function (&$value) use ($sanitizer): void {
            if (is_string($value)) {
                $value = $sanitizer->clean($value);
            }
        });

        $path = resource_path("lang/{$language->code}.json");
        File::put($path, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return redirect()->route('admin.languages.translations', $language)
            ->with('success', __('Translations updated successfully'));
    }

    public function setDefault(Language $language)
    {
        \DB::transaction(function () use ($language): void {
            Language::where('is_default', true)->update(['is_default' => false]);
            $language->update(['is_default' => true]);
        });

        return redirect()->route('admin.languages.index')
            ->with('success', __('Default language updated successfully'));
    }

    public function addTranslation(Request $request, Language $language)
    {
        $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string',
        ]);

        $translations = $this->getTranslations($language->code);
        // Sanitize the added value
        $translations[$request->key] = is_string($request->value) ?
            (new HtmlSanitizer)->clean($request->value) : $request->value;

        $path = resource_path("lang/{$language->code}.json");
        File::put($path, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return redirect()->route('admin.languages.translations', $language)
            ->with('success', __('Translation added successfully'));
    }

    public function deleteTranslation(Request $request, Language $language)
    {
        $key = $request->input('key');
        $translations = $this->getTranslations($language->code);

        if (isset($translations[$key])) {
            unset($translations[$key]);

            $path = resource_path("lang/{$language->code}.json");
            File::put($path, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return redirect()->route('admin.languages.translations', $language)
            ->with('success', __('Translation deleted successfully'));
    }

    public function refreshCache()
    {
        // Clear translation cache
        cache()->forget('translations');

        return response()->json([
            'success' => true,
            'message' => __('Translation cache refreshed successfully'),
        ]);
    }

    public function activate(Language $language)
    {
        $language->update(['is_active' => true]);

        return redirect()->route('admin.languages.edit', $language)
            ->with('success', __('Language activated successfully'));
    }

    public function deactivate(Language $language)
    {
        if ($language->is_default) {
            return redirect()->route('admin.languages.edit', $language)
                ->with('error', __('Cannot deactivate default language'));
        }

        $language->update(['is_active' => false]);

        return redirect()->route('admin.languages.edit', $language)
            ->with('success', __('Language deactivated successfully'));
    }

    public function bulkActivate(Request $request)
    {
        $ids = $request->input('ids', []);
        Language::whereIn('id', $ids)->update(['is_active' => true]);

        return redirect()->back()->with('success', __('Languages activated successfully'));
    }

    public function bulkDeactivate(Request $request)
    {
        $ids = $request->input('ids', []);
        Language::whereIn('id', $ids)->where('is_default', false)->update(['is_active' => false]);

        return redirect()->back()->with('success', __('Languages deactivated successfully'));
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        Language::whereIn('id', $ids)->where('is_default', false)->delete();

        return redirect()->back()->with('success', __('Languages deleted successfully'));
    }

    private function getTranslations($langCode)
    {
        $path = resource_path("lang/{$langCode}.json");
        if (File::exists($path)) {
            $content = File::get($path);

            return json_decode($content, true) ?: [];
        }

        return [];
    }

    private function createTranslationFile($langCode): void
    {
        $defaultTranslations = [
            'Welcome' => 'Welcome',
            'Dashboard' => 'Dashboard',
            'Users Management' => 'Users Management',
            'All Users' => 'All Users',
            'Add User' => 'Add User',
            'Edit User' => 'Edit User',
            'Languages' => 'Languages',
            'Languages Management' => 'Languages Management',
            'Add Language' => 'Add Language',
            'Edit Language' => 'Edit Language',
            'Manage Translations' => 'Manage Translations',
            'Currencies' => 'Currencies',
            'Currencies Management' => 'Currencies Management',
            'Add Currency' => 'Add Currency',
            'Edit Currency' => 'Edit Currency',
            'Exchange Rate' => 'Exchange Rate',
            'Default Currency' => 'Default Currency',
            'Settings' => 'Settings',
            'Profile' => 'Profile',
            'Logout' => 'Logout',
            'Name' => 'Name',
            'Code' => 'Code',
            'Flag' => 'Flag',
            'Symbol' => 'Symbol',
            'Actions' => 'Actions',
            'Edit' => 'Edit',
            'Delete' => 'Delete',
            'Create' => 'Create',
            'Update' => 'Update',
            'Cancel' => 'Cancel',
            'Save' => 'Save',
            'Active' => 'Active',
            'Default' => 'Default',
            'Status' => 'Status',
            'Yes' => 'Yes',
            'No' => 'No',
            'Email' => 'Email',
            'Phone' => 'Phone',
            'Role' => 'Role',
            'Balance' => 'Balance',
            'Created At' => 'Created At',
            'Updated At' => 'Updated At',
            'Are you sure?' => 'Are you sure?',
            'This action cannot be undone' => 'This action cannot be undone',
            'Confirm' => 'Confirm',
            'Success' => 'Success',
            'Error' => 'Error',
            'Warning' => 'Warning',
            'Info' => 'Info',
        ];

        $path = resource_path("lang/{$langCode}.json");
        File::put($path, json_encode($defaultTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
