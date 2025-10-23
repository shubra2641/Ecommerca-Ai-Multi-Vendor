<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    public function switchLang(Request $request)
    {
        // Support both 'lang' and 'language' parameters for compatibility
        $langCode = $request->input('language') ?? $request->input('lang');
        $language = Language::where('code', $langCode)->first();
        if ($language) {
            session()->put('locale', $language->code);

            // Ensure a language JSON file exists for this language. If not, create a starter file.
            $path = resource_path("lang/{$language->code}.json");
            if (! \Illuminate\Support\Facades\File::exists($path)) {
                $this->createTranslationFile($language->code);
            }
        }

        return Redirect::back();
    }

    public function switch(Request $request)
    {
        return $this->switchLang($request);
    }

    public function index()
    {
        $languages = Language::orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return view('admin.languages.index', compact('languages'));
    }

    public function create()
    {
        return view('admin.languages.create');
    }

    public function store(Request $request)
    {
        $language = new Language();

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:2|unique:languages,code',
            'flag' => 'nullable|string|max:10',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // If setting as default, remove default from others
        if ($request->is_default) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        $language->fill($request->all());
        $language->save();

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

    public function updateTranslations(Request $request, Language $language)
    {
        $translations = $request->input('translations', []);

        $path = resource_path("lang/{$language->code}.json");
        File::put($path, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return redirect()->route('admin.languages.translations', $language)
            ->with('success', __('Translations updated successfully'));
    }

    public function setDefault(Language $language)
    {
        Language::where('is_default', true)->update(['is_default' => false]);
        $language->update(['is_default' => true]);

        return redirect()->route('admin.languages.index')
            ->with('success', __('Default language updated successfully'));
    }

    private function getTranslations($langCode)
    {
        $path = resource_path("lang/{$langCode}.json");
        if (File::exists($path)) {
            return json_decode(File::get($path), true);
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
            'Languages' => 'Languages',
            'Currencies' => 'Currencies',
            'Settings' => 'Settings',
            'Profile' => 'Profile',
            'Logout' => 'Logout',
            'Name' => 'Name',
            'Code' => 'Code',
            'Actions' => 'Actions',
            'Edit' => 'Edit',
            'Delete' => 'Delete',
            'Create' => 'Create',
            'Update' => 'Update',
            'Cancel' => 'Cancel',
            'Save' => 'Save',
        ];

        $path = resource_path("lang/{$langCode}.json");
        File::put($path, json_encode($defaultTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
