<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSlide;
use App\Services\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

    public function store(Request $request, HtmlSanitizer $sanitizer): RedirectResponse
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
        // sanitize i18n fields
        if (isset($data['title_i18n']) && is_array($data['title_i18n'])) {
            foreach ($data['title_i18n'] as $lc => $v) {
                $data['title_i18n'][$lc] = $sanitizer->clean($v);
            }
        }
        if (isset($data['subtitle_i18n']) && is_array($data['subtitle_i18n'])) {
            foreach ($data['subtitle_i18n'] as $lc => $v) {
                $data['subtitle_i18n'][$lc] = $sanitizer->clean($v);
            }
        }
        if (isset($data['button_text_i18n']) && is_array($data['button_text_i18n'])) {
            foreach ($data['button_text_i18n'] as $lc => $v) {
                $data['button_text_i18n'][$lc] = $sanitizer->clean($v);
            }
        }
        $data['title'] = $data['title_i18n'][$defaultLocale] ?? null;
        $data['subtitle'] = $data['subtitle_i18n'][$defaultLocale] ?? null;
        $data['button_text'] = $data['button_text_i18n'][$defaultLocale] ??
            null;
        HomepageSlide::create($data);
        Cache::forget('homepage_slides_enabled');

        return back()->with('success', __('Slide created.'));
    }

    public function update(Request $request, HomepageSlide $slide, HtmlSanitizer $sanitizer): RedirectResponse
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
        if ($request->hasFile('image')) {
            if ($slide->image && Storage::disk('public')->exists($slide->image)) {
                Storage::disk('public')->delete($slide->image);
            } $data['image'] = $request->file('image')->store('uploads/homepage/slides', 'public');
        }
        $data['enabled'] = (bool) ($data['enabled'] ?? false);
        $merge = function ($existing, $incoming) {
            $existing = $existing ? $existing : [];
            foreach (($incoming ? $incoming : []) as $k => $v) {
                if ($v === '') {
                    unset($existing[$k]);

                    continue;
                } if ($v !== null) {
                    $existing[$k] = $v;
                }
            }

            return $existing;
        };
        if (isset($data['title_i18n'])) {
            $data['title_i18n'] = $merge($slide->title_i18n, $data['title_i18n']);
            foreach ($data['title_i18n'] as $lc => $v) {
                $data['title_i18n'][$lc] = $sanitizer->clean($v);
            }
        }
        if (isset($data['subtitle_i18n'])) {
            $data['subtitle_i18n'] = $merge(
                $slide->subtitle_i18n,
                $data['subtitle_i18n']
            );
            foreach ($data['subtitle_i18n'] as $lc => $v) {
                $data['subtitle_i18n'][$lc] = $sanitizer->clean($v);
            }
        }
        if (isset($data['button_text_i18n'])) {
            $data['button_text_i18n'] = $merge(
                $slide->button_text_i18n,
                $data['button_text_i18n']
            );
            foreach ($data['button_text_i18n'] as $lc => $v) {
                $data['button_text_i18n'][$lc] = $sanitizer->clean($v);
            }
        }
        $defaultLocale = config('app.locale', 'en');
        $data['title'] = ($data['title_i18n'][$defaultLocale] ??
            $slide->title_i18n[$defaultLocale] ?? $slide->title);
        $data['subtitle'] = ($data['subtitle_i18n'][$defaultLocale] ??
            $slide->subtitle_i18n[$defaultLocale] ?? $slide->subtitle);
        $data['button_text'] = ($data['button_text_i18n'][$defaultLocale] ??
            $slide->button_text_i18n[$defaultLocale] ?? $slide->button_text);
        $slide->update($data);
        Cache::forget('homepage_slides_enabled');

        return back()->with('success', __('Slide updated.'));
    }

    public function destroy(HomepageSlide $slide): RedirectResponse
    {
        if ($slide->image && Storage::disk('public')->exists($slide->image)) {
            Storage::disk('public')->delete($slide->image);
        } $slide->delete();
        Cache::forget('homepage_slides_enabled');

        return back()->with('success', __('Slide deleted.'));
    }

    private function activeLanguages()
    {
        return Cache::remember('active_languages_full', 3600, function () {
            try {
                return \DB::table('languages')->where('is_active', 1)->orderBy('is_default', 'desc')->get();
            } catch (\Throwable $e) {
                return collect([
                    (object) [
                        'code' => config('app.locale', 'en'),
                        'is_default' => 1,
                        'name' => strtoupper(config('app.locale', 'en')),
                    ],
                ]);
            }
        });
    }
}
