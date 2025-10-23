<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomepageBannerController extends Controller
{

    public function index(): View
    {
        $banners = HomepageBanner::orderBy('sort_order')->get();
        $activeLanguages = $this->activeLanguages();

        return view('admin.homepage.banners.index', compact('banners', 'activeLanguages'));
    }

    public function store(Request $request, \App\Services\HtmlSanitizer $sanitizer): RedirectResponse
    {
        $data = $request->validate([
            'placement_key' => ['nullable', 'string', 'max:64'],
            'image' => ['required', 'image', 'max:2048'],
            'link_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'between:0,65535'],
            'enabled' => ['nullable', 'boolean'],
            'alt_text_i18n' => ['nullable', 'array'],
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/homepage/banners', 'public');
        } $data['enabled'] = (bool) ($data['enabled'] ?? false);
        // sanitize alt text translations
        if (isset($data['alt_text_i18n']) && is_array($data['alt_text_i18n'])) {
            foreach ($data['alt_text_i18n'] as $lc => $v) {
                if ($v === null) {
                    unset($data['alt_text_i18n'][$lc]);

                    continue;
                }
                $data['alt_text_i18n'][$lc] = is_string($v) ?
                $sanitizer->clean($v) : $v;
            }
            if (empty($data['alt_text_i18n'])) {
                unset($data['alt_text_i18n']);
            }
        }
        $defaultLocale = config('app.locale', 'en');
        $data['alt_text'] = $data['alt_text_i18n'][$defaultLocale] ??
            null;
        HomepageBanner::create($data);
        Cache::forget('homepage_banners_enabled');

        return back()->with('success', __('Banner created.'));
    }

    public function update(
        Request $request,
        HomepageBanner $banner,
        \App\Services\HtmlSanitizer $sanitizer
    ): RedirectResponse {
        $data = $request->validate([
            'placement_key' => ['nullable', 'string', 'max:64'],
            'image' => ['nullable', 'image', 'max:2048'],
            'link_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'between:0,65535'],
            'enabled' => ['nullable', 'boolean'],
            'alt_text_i18n' => ['nullable', 'array'],
        ]);
        if ($request->hasFile('image')) {
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            } $data['image'] = $request->file('image')->store('uploads/homepage/banners', 'public');
        } $data['enabled'] = (bool) ($data['enabled'] ?? false);
        $merge = function ($e, $i) {
            $e = $e ? $e : [];
            foreach (($i ? $i : []) as $k => $v) {
                if ($v === '') {
                    unset($e[$k]);

                    continue;
                }
                if ($v !== null) {
                    $e[$k] = $v;
                }
            }

            return $e;
        };
        if (isset($data['alt_text_i18n'])) {
            $data['alt_text_i18n'] = $merge(
                $banner->alt_text_i18n,
                $data['alt_text_i18n']
            );
            // sanitize merged translations
            foreach ($data['alt_text_i18n'] as $lc => $v) {
                $data['alt_text_i18n'][$lc] = is_string($v) ? $sanitizer->clean($v) : $v;
            }
        }
        $defaultLocale = config('app.locale', 'en');
        $data['alt_text'] = $data['alt_text_i18n'][$defaultLocale] ??
            $banner->alt_text_i18n[$defaultLocale] ?? $banner->alt_text;
        $banner->update($data);
        Cache::forget('homepage_banners_enabled');

        return back()->with('success', __('Banner updated.'));
    }

    public function destroy(HomepageBanner $banner): RedirectResponse
    {
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        } $banner->delete();
        Cache::forget('homepage_banners_enabled');

        return back()->with('success', __('Banner deleted.'));
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
