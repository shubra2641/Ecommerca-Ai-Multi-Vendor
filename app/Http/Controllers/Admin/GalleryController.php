<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use App\Services\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage; // If using intervention/image (needs composer require intervention/image)
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

// For GD driver

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $tag = trim($request->get('tag', ''));
        $query = GalleryImage::query();
        if ($q !== '') {
            $query->where(function ($qq) use ($q): void {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('alt', 'like', "%{$q}%")
                    ->orWhere('tags', 'like', "%{$q}%");
            });
        }
        if ($tag !== '') {
            $query->where('tags', 'like', "%{$tag}%");
        }
        $images = $query->latest()->paginate(30)->appends(['q' => $q, 'tag' => $tag]);
        $distinctTags = GalleryImage::select('tags')->whereNotNull('tags')->pluck('tags')->flatMap(function ($row) {
            return array_filter(array_map('trim', explode(',', $row)));
        })->unique()->sort()->values();

        return view('admin.gallery.index', compact('images', 'q', 'tag', 'distinctTags'));
    }

    public function create()
    {
        return view('admin.gallery.upload');
    }

    public function store(Request $request, HtmlSanitizer $sanitizer): RedirectResponse
    {
        // Support single or multiple uploads: image or images[]
        $isMultiple = $request->hasFile('images');

        $rules = [
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'alt' => ['nullable', 'string', 'max:150'],
            'tags' => ['nullable', 'string', 'max:255'],
        ];
        if ($isMultiple) {
            $rules['images'] = ['required', 'array', 'max:15'];
            $rules['images.*'] = ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];
        } else {
            $rules['image'] = ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];
        }
        $request->validate($rules);

        $files = $isMultiple ? $request->file('images') : [$request->file('image')];
        $meta = $this->sanitizeMeta($request->only('title', 'description', 'alt', 'tags'), $sanitizer);
        $count = 0;

        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $this->persistGalleryUpload($file, $meta);
            $count++;
        }

        return redirect()->route('admin.gallery.index')->with('success', __(':n image(s) uploaded.', ['n' => $count]));
    }

    public function quickStore(Request $request, HtmlSanitizer $sanitizer)
    {
        $rules = [
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'alt' => ['nullable', 'string', 'max:150'],
            'tags' => ['nullable', 'string', 'max:255'],
            'image' => ['required_without:images', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'images' => ['nullable', 'array', 'max:15'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];

        $request->validate($rules);

        $files = $request->hasFile('images') ? $request->file('images') : [$request->file('image')];
        $files = array_filter($files);

        if (! count($files)) {
            return response()->json([
                'success' => false,
                'message' => __('No files were uploaded.'),
            ], 422);
        }

        $meta = $this->sanitizeMeta($request->only('title', 'description', 'alt', 'tags'), $sanitizer);
        $stored = [];

        foreach ($files as $file) {
            $image = $this->persistGalleryUpload($file, $meta);
            $stored[] = $this->formatGalleryResponse($image);
        }

        return response()->json([
            'success' => true,
            'files' => $stored,
        ]);
    }

    public function edit(GalleryImage $image)
    {
        return view('admin.gallery.edit', compact('image'));
    }

    public function update(Request $request, GalleryImage $image, HtmlSanitizer $sanitizer): RedirectResponse
    {
        $request->validate([
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'alt' => ['nullable', 'string', 'max:150'],
            'tags' => ['nullable', 'string', 'max:255'],
        ]);
        $payload = $request->only('title', 'description', 'alt', 'tags');
        if (isset($payload['title']) && is_string($payload['title']) && $payload['title'] !== '') {
            $payload['title'] = $sanitizer->clean($payload['title']);
        }
        if (isset($payload['description']) && is_string($payload['description']) && $payload['description'] !== '') {
            $payload['description'] = $sanitizer->clean($payload['description']);
        }
        if (isset($payload['alt']) && is_string($payload['alt']) && $payload['alt'] !== '') {
            $payload['alt'] = $sanitizer->clean($payload['alt']);
        }
        if (isset($payload['tags']) && is_string($payload['tags']) && $payload['tags'] !== '') {
            $parts = array_map('trim', explode(',', $payload['tags']));
            $clean = [];
            foreach ($parts as $p) {
                if ($p === '') {
                    continue;
                }
                $clean[] = $sanitizer->clean($p);
            }
            $payload['tags'] = implode(',', $clean);
        }
        $image->update($payload);

        return back()->with('success', __('Updated.'));
    }

    public function destroy(GalleryImage $image): RedirectResponse
    {
        $setting = \App\Models\Setting::first();
        $inUse = $setting && $setting->logo &&
            ($setting->logo === $image->webp_path || $setting->logo === $image->original_path);

        if ($inUse && request()->query('force') !== '1') {
            return back()->with(
                'warning',
                __('This image is currently used as the site logo. Confirm deletion to proceed.')
            );
        }

        if ($inUse && request()->query('force') === '1') {
            // Clear logo reference before deleting files
            $setting->logo = null;
            $setting->save();
        }

        if ($image->original_path && Storage::disk('public')->exists($image->original_path)) {
            Storage::disk('public')->delete($image->original_path);
        }
        if ($image->webp_path && Storage::disk('public')->exists($image->webp_path)) {
            Storage::disk('public')->delete($image->webp_path);
        }
        $image->delete();

        return back()->with('success', __('Image deleted.'));
    }

    public function deleteLogo(Request $request): RedirectResponse
    {
        $setting = \App\Models\Setting::first();
        if ($setting && $setting->logo && Storage::disk('public')->exists($setting->logo)) {
            Storage::disk('public')->delete($setting->logo);
            $setting->logo = null;
            $setting->save();
        }

        return back()->with('success', __('Logo deleted.'));
    }

    public function useAsLogo(GalleryImage $image): RedirectResponse
    {
        $setting = \App\Models\Setting::first();
        if (! $setting) {
            $setting = new \App\Models\Setting;
        }
        // delete old logo file if exists and different
        if (
            $setting->logo && $setting->logo !== $image->original_path &&
            \Storage::disk('public')->exists($setting->logo)
        ) {
            \Storage::disk('public')->delete($setting->logo);
        }
        // prefer webp if available
        $setting->logo = $image->webp_path ?: $image->original_path;
        $setting->save();

        return redirect()->route('admin.settings.index')->with('success', __('Logo updated from gallery.'));
    }

    private function sanitizeMeta(array $meta, HtmlSanitizer $sanitizer): array
    {
        $clean = [
            'title' => null,
            'description' => null,
            'alt' => null,
            'tags' => null,
        ];

        if (! empty($meta['title']) && is_string($meta['title'])) {
            $clean['title'] = $sanitizer->clean($meta['title']);
        }

        if (! empty($meta['description']) && is_string($meta['description'])) {
            $clean['description'] = $sanitizer->clean($meta['description']);
        }

        if (! empty($meta['alt']) && is_string($meta['alt'])) {
            $clean['alt'] = $sanitizer->clean($meta['alt']);
        }

        if (! empty($meta['tags']) && is_string($meta['tags'])) {
            $parts = array_map('trim', explode(',', $meta['tags']));
            $cleanParts = [];

            foreach ($parts as $part) {
                if ($part === '') {
                    continue;
                }

                $cleanParts[] = $sanitizer->clean($part);
            }

            if ($cleanParts) {
                $clean['tags'] = implode(',', $cleanParts);
            }
        }

        return $clean;
    }

    private function persistGalleryUpload(UploadedFile $file, array $meta): GalleryImage
    {
        $originalPath = $file->store('gallery/original', 'public');
        $webpPath = null;
        $thumbPath = null;

        try {
            $manager = new ImageManager(new Driver);
            $imageObj = $manager->read($file->getRealPath());

            $webpFileName = pathinfo($file->hashName(), PATHINFO_FILENAME).'.webp';
            $webpRelative = 'gallery/webp/'.$webpFileName;
            $imageObj->toWebp(85)->save(Storage::disk('public')->path($webpRelative));
            $webpPath = $webpRelative;

            $thumbClone = $imageObj->clone();
            $thumbClone->scale(320, 320, function ($constraint): void {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $thumbFileName = pathinfo($file->hashName(), PATHINFO_FILENAME).'_thumb.jpg';
            $thumbRelative = 'gallery/thumbs/'.$thumbFileName;
            $thumbClone->toJpeg(75)->save(Storage::disk('public')->path($thumbRelative));
            $thumbPath = $thumbRelative;
        } catch (\Throwable $e) {
            // leave optional derivatives empty if processing fails
        }

        return GalleryImage::create([
            'original_path' => $originalPath,
            'webp_path' => $webpPath,
            'thumbnail_path' => $thumbPath,
            'title' => $meta['title'],
            'description' => $meta['description'],
            'alt' => $meta['alt'],
            'tags' => $meta['tags'],
            'filesize' => $file->getSize(),
            'mime' => $file->getMimeType(),
        ]);
    }

    private function formatGalleryResponse(GalleryImage $image): array
    {
        $path = $image->webp_path ?: $image->original_path;

        $url = $path ? asset('storage/'.ltrim($path, '/')) : null;
        $thumbPath = $image->thumbnail_path ? asset('storage/'.ltrim($image->thumbnail_path, '/')) : null;
        $thumb = $thumbPath ? $thumbPath : $url;

        return [
            'id' => $image->id,
            'path' => $path,
            'url' => $url,
            'thumbnail' => $thumb,
            'title' => $image->title,
            'alt' => $image->alt,
        ];
    }
}
