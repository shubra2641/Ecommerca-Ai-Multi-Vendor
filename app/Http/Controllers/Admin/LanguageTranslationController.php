<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

final class LanguageTranslationController extends Controller
{
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

    private function getTranslations(string $langCode): array
    {
        $path = resource_path("lang/{$langCode}.json");
        if (File::exists($path)) {
            $content = File::get($path);

            return json_decode($content, true) ? json_decode($content, true) : [];
        }

        return [];
    }

    private function saveTranslations(string $langCode, array $translations): void
    {
        $path = resource_path("lang/{$langCode}.json");
        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
        File::put($path, json_encode($translations, $options));
    }

    private function sanitizeTranslations(array &$translations, HtmlSanitizer $sanitizer): void
    {
        $sanitizeValue = function (&$value) use ($sanitizer): void {
            if (is_string($value)) {
                $value = $sanitizer->clean($value);
            }
        };

        array_walk_recursive($translations, $sanitizeValue);
    }
}