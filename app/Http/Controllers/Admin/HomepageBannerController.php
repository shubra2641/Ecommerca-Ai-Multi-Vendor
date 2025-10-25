<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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

    public function store(Request $request): RedirectResponse
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
        }
        $data['enabled'] = (bool) ($data['enabled'] ?? false);
        $defaultLocale = config('app.locale', 'en');
        $data['alt_text'] = $data['alt_text_i18n'][$defaultLocale] ??
            null;
        HomepageBanner::create($data);
        Cache::forget('homepage_banners_enabled');

        return back()->with('success', __('Banner created.'));
    }

    public function update(Request $request, HomepageBanner $banner): RedirectResponse
    {
        $data = $request->validate([
            'placement_key' => ['nullable', 'string', 'max:64'],
            'image' => ['nullable', 'image', 'max:2048'],
            'link_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'between:0,65535'],
            'enabled' => ['nullable', 'boolean'],
            'alt_text_i18n' => ['nullable', 'array'],
        ]);

        $this->handleImageUpload($request, $banner, $data);
        $this->handleAltTextI18n($data, $banner);

        $data['enabled'] = (bool) ($data['enabled'] ?? false);
        $banner->update($data);
        Cache::forget('homepage_banners_enabled');

        return back()->with('success', __('Banner updated.'));
    }

    public function destroy(HomepageBanner $banner): RedirectResponse
    {
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();
        Cache::forget('homepage_banners_enabled');

        return back()->with('success', __('Banner deleted.'));
    }

    private function handleImageUpload(Request $request, HomepageBanner $banner, array &$data): void
    {
        if ($request->hasFile('image')) {
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('uploads/homepage/banners', 'public');
        }
    }

    private function handleAltTextI18n(array &$data, HomepageBanner $banner): void
    {
        if (isset($data['alt_text_i18n'])) {
            $data['alt_text_i18n'] = $this->mergeI18nData(
                $banner->alt_text_i18n,
                $data['alt_text_i18n']
            );
        }

        $defaultLocale = config('app.locale', 'en');
        $data['alt_text'] = $data['alt_text_i18n'][$defaultLocale] ??
            $banner->alt_text_i18n[$defaultLocale] ?? $banner->alt_text;
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
