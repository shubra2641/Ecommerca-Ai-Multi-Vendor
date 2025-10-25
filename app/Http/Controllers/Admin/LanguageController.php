<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

final class LanguageController extends Controller
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
        $this->validateLanguageData($request, null);
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
        $this->validateLanguageData($request, $language);
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

    public function makeDefault(Language $language)
    {
        DB::transaction(function () use ($language): void {
            $this->handleDefaultLanguage(true);
            $language->update(['is_default' => true]);
        });

        return redirect()->route('admin.languages.index')
            ->with('success', __('Default language updated successfully'));
    }

    private function handleDefaultLanguage(bool $setDefault): void
    {
        if ($setDefault) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }
    }

    private function getTranslations(string $langCode): array
    {
        $path = resource_path("lang/{$langCode}.json");
        if (File::exists($path)) {
            $content = File::get($path);

            return json_decode($content, true) ? json_decode($content, true) : [];
        }

        return [];
    }

    private function createTranslationFile(string $langCode): void
    {
        $defaultTranslations = [
            'Welcome' => 'Welcome',
            'Dashboard' => 'Dashboard',
        ];

        $path = resource_path("lang/{$langCode}.json");
        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
        File::put($path, json_encode($defaultTranslations, $options));
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
}
