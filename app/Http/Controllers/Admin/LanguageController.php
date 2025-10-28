<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
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

        // Add translations count manually
        $languages->getCollection()->transform(function ($language) {
            $language->translations_count = $this->getLanguageTranslationsCount($language->code);
            return $language;
        });

        $totalTranslations = $this->getTotalTranslations();

        return view('admin.languages.index', compact('languages', 'totalTranslations'));
    }

    public function create()
    {
        return view('admin.languages.create');
    }

    public function store(Request $request)
    {
        $this->validateLanguageData($request, null);

        DB::transaction(function () use ($request): void {
            $this->handleDefaultLanguage($request->is_default);

            $language = Language::create($request->all());

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

    public function update(Request $request, Language $language)
    {
        $this->validateLanguageData($request, $language);

        DB::transaction(function () use ($request, $language): void {
            // If setting as default, remove default from others
            if ($request->is_default && ! $language->is_default) {
                $this->handleDefaultLanguage(true);
            }

            $language->update($request->all());
        });

        return redirect()->route('admin.languages.index')
            ->with('success', __('Language updated successfully'));
    }

    public function destroy(Language $language): \Illuminate\Http\RedirectResponse
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

    public function makeDefault(Language $language): \Illuminate\Http\RedirectResponse
    {
        DB::transaction(function () use ($language): void {
            $this->handleDefaultLanguage(true);
            $language->update(['is_default' => true]);
        });

        return redirect()->route('admin.languages.index')
            ->with('success', __('Default language updated successfully'));
    }

    public function setDefault(Request $request, Language $language): \Illuminate\Http\RedirectResponse
    {
        return $this->makeDefault($language);
    }

    public function activate(Language $language): \Illuminate\Http\RedirectResponse
    {
        $language->update(['is_active' => true]);

        return redirect()->route('admin.languages.index')
            ->with('success', __('Language activated successfully'));
    }

    public function deactivate(Language $language): \Illuminate\Http\RedirectResponse
    {
        if ($language->is_default) {
            return redirect()->route('admin.languages.index')
                ->with('error', __('Cannot deactivate default language'));
        }

        $language->update(['is_active' => false]);

        return redirect()->route('admin.languages.index')
            ->with('success', __('Language deactivated successfully'));
    }

    private function handleDefaultLanguage(bool $setDefault): void
    {
        if ($setDefault) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }
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

    /**
     * @return array<string, string|bool>
     */
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

    public function translations(Language $language)
    {
        $translations = $this->getTranslations($language->code);

        return view('admin.languages.translations', compact('language', 'translations'));
    }

    public function updateTranslations(Request $request, Language $language)
    {
        $request->validate([
            'translations' => 'required|array',
            'translations.*' => 'string',
        ]);

        $this->saveTranslations($language->code, $request->translations);

        return redirect()->back()->with('success', __('Translations updated successfully'));
    }

    public function addTranslation(Request $request, Language $language)
    {
        $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required|string',
        ]);

        $translations = $this->getTranslations($language->code);
        $translations[$request->key] = $request->value;

        $this->saveTranslations($language->code, $translations);

        return redirect()->back()->with('success', __('Translation added successfully'));
    }

    public function deleteTranslation(Request $request, Language $language)
    {
        $request->validate([
            'key' => 'required|string',
        ]);

        $translations = $this->getTranslations($language->code);
        unset($translations[$request->key]);

        $this->saveTranslations($language->code, $translations);

        return redirect()->back()->with('success', __('Translation deleted successfully'));
    }

    public function aiTranslate(Request $request, Language $language)
    {
        $request->validate([
            'translations' => 'required|array',
        ]);

        try {
            $aiService = app(\App\Services\AI\SimpleAIService::class);
            $translatedKeys = [];

            foreach ($request->translations as $key => $value) {
                // Skip empty keys
                if (empty($key)) {
                    continue;
                }

                // Generate translation for this key
                $result = $aiService->generate($key, 'translation', $language->code);

                if (isset($result['translation'])) {
                    $translatedKeys[$key] = $result['translation'];
                } elseif (isset($result['description'])) {
                    // Fallback to description if translation key not found
                    $translatedKeys[$key] = $result['description'];
                } else {
                    // Keep original value if AI fails
                    $translatedKeys[$key] = $value;
                }
            }

            // Save the translated keys
            $this->saveTranslations($language->code, $translatedKeys);

            return redirect()->back()->with('success', __('All translations have been updated with AI!'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Translation failed: ') . $e->getMessage());
        }
    }

    private function getTranslations(string $langCode): array
    {
        $path = resource_path("lang/{$langCode}.json");

        if (!File::exists($path)) {
            return [];
        }

        $content = File::get($path);
        $translations = json_decode($content, true);

        return is_array($translations) ? $translations : [];
    }

    private function saveTranslations(string $langCode, array $translations): void
    {
        $path = resource_path("lang/{$langCode}.json");
        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
        File::put($path, json_encode($translations, $options));
    }

    private function getLanguageTranslationsCount(string $langCode): int
    {
        $translations = $this->getTranslations($langCode);
        return count($translations);
    }

    private function getTotalTranslations(): int
    {
        $total = 0;
        $languages = Language::where('is_active', true)->get();

        foreach ($languages as $language) {
            $total += $this->getLanguageTranslationsCount($language->code);
        }

        return $total;
    }
}
