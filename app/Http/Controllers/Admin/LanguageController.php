<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $validated = $this->validateLanguageData($request, null);
        $clean = $this->sanitizeLanguageData($request, $sanitizer);

        DB::transaction(function () use ($request, $clean): void {
            $this->handleDefaultLanguage($request->is_default);

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
        $validated = $this->validateLanguageData($request, $language);
        $clean = $this->sanitizeLanguageData($request, $sanitizer);

        DB::transaction(function () use ($request, $language, $clean): void {
            // If setting as default, remove default from others
            if ($request->is_default && ! $language->is_default) {
                $this->handleDefaultLanguage(true);
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
        $this->sanitizeTranslations($translations, $sanitizer);
        $this->saveTranslations($language->code, $translations);

        return redirect()->route('admin.languages.translations', $language)
            ->with('success', __('Translations updated successfully'));
    }

    public function makeDefault(Language $language)
    {
        DB::transaction(function () use ($language): void {
            $this->handleDefaultLanguage(true);
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
            (new HtmlSanitizer())->clean($request->value) : $request->value;

        $this->saveTranslations($language->code, $translations);

        return redirect()->route('admin.languages.translations', $language)
            ->with('success', __('Translation added successfully'));
    }

    public function deleteTranslation(Request $request, Language $language)
    {
        $key = $request->input('key');
        $translations = $this->getTranslations($language->code);

        if (isset($translations[$key])) {
            unset($translations[$key]);
            $this->saveTranslations($language->code, $translations);
        }

        return redirect()->route('admin.languages.translations', $language)
            ->with('success', __('Translation deleted successfully'));
    }

    public function toggleActive(Language $language, bool $activate)
    {
        if ($activate && ! $language->is_active) {
            $language->update(['is_active' => true]);
            $message = __('Language activated successfully');
        } elseif (! $activate && $language->is_active) {
            if ($language->is_default) {
                return redirect()->route('admin.languages.edit', $language)
                    ->with('error', __('Cannot deactivate default language'));
            }
            $language->update(['is_active' => false]);
            $message = __('Language deactivated successfully');
        } else {
            $message = $activate ? __('Language is already active') : __('Language is already inactive');
        }

        return redirect()->route('admin.languages.edit', $language)
            ->with('success', $message);
    }

    public function activate(Language $language)
    {
        return $this->toggleActive($language, true);
    }

    public function deactivate(Language $language)
    {
        return $this->toggleActive($language, false);
    }

    public function bulkActivate(Request $request)
    {
        $ids = $this->validateBulkIds($request);
        Language::whereIn('id', $ids)->update(['is_active' => true]);

        return redirect()->back()->with('success', __('Languages activated successfully'));
    }

    public function bulkDeactivate(Request $request)
    {
        $ids = $this->validateBulkIds($request);
        $nonDefaultIds = $this->getNonDefaultLanguageIds($ids);
        Language::whereIn('id', $nonDefaultIds)->update(['is_active' => false]);

        return redirect()->back()->with('success', __('Languages deactivated successfully'));
    }

    public function bulkDelete(Request $request)
    {
        $ids = $this->validateBulkIds($request);
        $nonDefaultIds = $this->getNonDefaultLanguageIds($ids);
        Language::whereIn('id', $nonDefaultIds)->delete();

        return redirect()->back()->with('success', __('Languages deleted successfully'));
    }

    private function getTranslations($langCode)
    {
        $path = resource_path("lang/{$langCode}.json");
        if (File::exists($path)) {
            $content = File::get($path);

            return json_decode($content, true) ? json_decode($content, true) : [];
        }

        return [];
    }

    private function createTranslationFile($langCode): void
    {
        $defaultTranslations = [
            'Welcome' => 'Welcome',
            'Dashboard' => 'Dashboard',
        ];

        $path = resource_path("lang/{$langCode}.json");
        File::put($path, json_encode($defaultTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function validateLanguageData(Request $request, ?Language $language = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:2',
            'flag' => 'nullable|string|max:10',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];

        if ($language) {
            $rules['code'] .= ',code,' . $language->id;
        } else {
            $rules['code'] .= '|unique:languages,code';
        }

        return $request->validate($rules);
    }

    private function sanitizeLanguageData(Request $request, HtmlSanitizer $sanitizer): array
    {
        $clean = [];
        foreach (['name', 'code', 'flag'] as $key) {
            if ($request->has($key) && is_string($request->input($key))) {
                $clean[$key] = $sanitizer->clean($request->input($key));
            }
        }

        return $clean;
    }

    private function validateBulkIds(Request $request): array
    {
        return $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:languages,id',
        ])['ids'];
    }

    private function saveTranslations(string $langCode, array $translations): void
    {
        $path = resource_path("lang/{$langCode}.json");
        File::put($path, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function sanitizeTranslations(array &$translations, HtmlSanitizer $sanitizer): void
    {
        array_walk_recursive($translations, function (&$value) use ($sanitizer): void {
            if (is_string($value)) {
                $value = $sanitizer->clean($value);
            }
        });
    }
}
