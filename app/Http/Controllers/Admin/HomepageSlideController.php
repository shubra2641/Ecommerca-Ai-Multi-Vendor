<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomepageSlideController extends Controller
{
    public function index(): View
    {
        $slides = HomepageSlide::orderBy('sort_order')->get();
        $activeLanguages = $this->activeLanguages();

        return view('admin.homepage.slides.index', compact('slides', 'activeLanguages'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'max:2048'],
            'link_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'between:0,65535'],
            'enabled' => ['nullable', 'boolean'],
            'title_i18n' => ['nullable', 'array'],
            'subtitle_i18n' => ['nullable', 'array'],
            'button_text_i18n' => ['nullable', 'array'],
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/homepage/slides', 'public');
        }
        $data['enabled'] = (bool) ($data['enabled'] ?? false);
        // derive single-language fallbacks
        $defaultLocale = config('app.locale', 'en');
        $data['title'] = $data['title_i18n'][$defaultLocale] ?? null;
        $data['subtitle'] = $data['subtitle_i18n'][$defaultLocale] ?? null;
        $data['button_text'] = $data['button_text_i18n'][$defaultLocale] ??
            null;
        HomepageSlide::create($data);
        Cache::forget('homepage_slides_enabled');

        return back()->with('success', __('Slide created.'));
    }

    public function update(Request $request, HomepageSlide $slide): RedirectResponse
    {
        $data = $request->validate([
            'image' => ['nullable', 'image', 'max:2048'],
            'link_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'between:0,65535'],
            'enabled' => ['nullable', 'boolean'],
            'title_i18n' => ['nullable', 'array'],
            'subtitle_i18n' => ['nullable', 'array'],
            'button_text_i18n' => ['nullable', 'array'],
        ]);

        $this->handleImageUpload($request, $slide, $data);
        $this->handleI18nFields($data, $slide);

        $data['enabled'] = (bool) ($data['enabled'] ?? false);
        $slide->update($data);
        Cache::forget('homepage_slides_enabled');

        return back()->with('success', __('Slide updated.'));
    }

    public function destroy(HomepageSlide $slide): RedirectResponse
    {
        if ($slide->image && Storage::disk('public')->exists($slide->image)) {
            Storage::disk('public')->delete($slide->image);
        }
        $slide->delete();
        Cache::forget('homepage_slides_enabled');

        return back()->with('success', __('Slide deleted.'));
    }

    private function handleImageUpload(Request $request, HomepageSlide $slide, array &$data): void
    {
        if ($request->hasFile('image')) {
            if ($slide->image && Storage::disk('public')->exists($slide->image)) {
                Storage::disk('public')->delete($slide->image);
            }
            $data['image'] = $request->file('image')->store('uploads/homepage/slides', 'public');
        }
    }

    private function handleI18nFields(array &$data, HomepageSlide $slide): void
    {
        $i18nFields = ['title_i18n', 'subtitle_i18n', 'button_text_i18n'];
        $defaultLocale = config('app.locale', 'en');

        foreach ($i18nFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $this->mergeI18nData(
                    $slide->{$field},
                    $data[$field]
                );
            }

            $baseField = str_replace('_i18n', '', $field);
            $data[$baseField] = ($data[$field][$defaultLocale] ??
                $slide->{$field}[$defaultLocale] ?? $slide->{$baseField});
        }
    }

    private function mergeI18nData(?array $existing, ?array $incoming): array
    {
        $existing = $existing ?: [];
        $incoming = $incoming ?: [];

        foreach ($incoming as $key => $value) {
            if ($value === '') {
                unset($existing[$key]);
                continue;
            }
            if ($value !== null) {
                $existing[$key] = $value;
            }
        }

        return $existing;
    }

    private function activeLanguages()
    {
        return Cache::remember('active_languages_full', 3600, function () {
            return \App\Models\Language::where('is_active', 1)->orderByDesc('is_default')->get();
        });
    }
}
