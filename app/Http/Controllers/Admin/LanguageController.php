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

        return view('admin.languages.index', compact('languages'));
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
}
