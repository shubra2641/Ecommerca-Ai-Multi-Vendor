<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
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
        $images = GalleryImage::latest()->paginate(30);

        return view('admin.gallery.index', compact('images'));
    }

    public function create()
    {
        return view('admin.gallery.upload');
    }

    public function store(Request $request): RedirectResponse
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
        $meta = $request->only('title', 'description', 'alt', 'tags');
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

    public function quickStore(Request $request)
    {
        $allowAnyFile = $request->boolean('allow_any_file', false);

        $rules = [
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'alt' => ['nullable', 'string', 'max:150'],
            'tags' => ['nullable', 'string', 'max:255'],
        ];

        if ($allowAnyFile) {
            $rules['image'] = ['required_without:images', 'file', 'max:51200']; // 50MB max for any file
            $rules['images'] = ['nullable', 'array', 'max:15'];
            $rules['images.*'] = ['file', 'max:51200'];
        } else {
            $rules['image'] = ['required_without:images', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];
            $rules['images'] = ['nullable', 'array', 'max:15'];
            $rules['images.*'] = ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];
        }

        $request->validate($rules);

        $files = $request->hasFile('images') ? $request->file('images') : [$request->file('image')];
        $files = array_filter($files);

        if (! count($files)) {
            return response()->json([
                'success' => false,
                'message' => __('No files were uploaded.'),
            ], 422);
        }

        $meta = $request->only('title', 'description', 'alt', 'tags');
        $stored = [];

        foreach ($files as $file) {
            if ($allowAnyFile) {
                // Handle any file type (for digital products)
                $path = $file->store('downloads', 'public');
                $stored[] = [
                    'id' => null, // No gallery entry for non-images
                    'path' => $path,
                    'url' => \App\Helpers\GlobalHelper::storageImageUrl($path),
                    'thumbnail' => null,
                    'title' => $meta['title'] ?: $file->getClientOriginalName(),
                    'alt' => $meta['alt'] ?: $file->getClientOriginalName(),
                ];
            } else {
                // Handle images (existing gallery functionality)
                $image = $this->persistGalleryUpload($file, $meta);
                $stored[] = $this->formatGalleryResponse($image);
            }
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

    public function update(Request $request, GalleryImage $image): RedirectResponse
    {
        $request->validate([
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'alt' => ['nullable', 'string', 'max:150'],
            'tags' => ['nullable', 'string', 'max:255'],
        ]);
        $payload = $request->only('title', 'description', 'alt', 'tags');
        $image->update($payload);

        return back()->with('success', __('Updated.'));
    }

    public function destroy(GalleryImage $image): RedirectResponse
    {
        $setting = \App\Models\Setting::first();
        $inUse = $this->isImageUsedAsLogo($image, $setting);

        if ($inUse && request()->query('force') !== '1') {
            return back()->with(
                'warning',
                __('This image is currently used as the site logo. Confirm deletion to proceed.')
            );
        }

        if ($inUse && request()->query('force') === '1') {
            $this->clearLogoReference($setting);
        }

        $this->deleteImageFiles($image);
        $image->delete();

        return back()->with('success', __('Image deleted.'));
    }

    public function deleteLogo(): RedirectResponse
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
            $setting = new \App\Models\Setting();
        }
        // delete old logo file if exists and different
        if (
            $setting->logo && $setting->logo !== $image->original_path &&
            \Storage::disk('public')->exists($setting->logo)
        ) {
            \Storage::disk('public')->delete($setting->logo);
        }
        // prefer webp if available
        $setting->logo = $image->webp_path ? $image->webp_path : $image->original_path;
        $setting->save();

        return redirect()->route('admin.settings.index')->with('success', __('Logo updated from gallery.'));
    }

    private function persistGalleryUpload(UploadedFile $file, array $meta): GalleryImage
    {
        $originalPath = $file->store('gallery/original', 'public');
        $webpPath = null;
        $thumbPath = null;

        $manager = new ImageManager(new Driver());
        $imageObj = $manager->read($file->getRealPath());

        $webpFileName = pathinfo($file->hashName(), PATHINFO_FILENAME) . '.webp';
        $webpRelative = 'gallery/webp/' . $webpFileName;
        $imageObj->toWebp(85)->save(Storage::disk('public')->path($webpRelative));
        $webpPath = $webpRelative;

        $thumbClone = $imageObj->clone();
        $thumbClone->scale(320, 320, function ($constraint): void {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $thumbFileName = pathinfo($file->hashName(), PATHINFO_FILENAME) . '_thumb.jpg';
        $thumbRelative = 'gallery/thumbs/' . $thumbFileName;
        $thumbClone->toJpeg(75)->save(Storage::disk('public')->path($thumbRelative));
        $thumbPath = $thumbRelative;

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

    private function isImageUsedAsLogo(GalleryImage $image, ?\App\Models\Setting $setting): bool
    {
        return $setting && $setting->logo &&
            ($setting->logo === $image->webp_path || $setting->logo === $image->original_path);
    }

    private function clearLogoReference(\App\Models\Setting $setting): void
    {
        $setting->logo = null;
        $setting->save();
    }

    private function formatGalleryResponse(GalleryImage $image): array
    {
        return [
            'id' => $image->id,
            'path' => $image->original_path,
            'url' => \App\Helpers\GlobalHelper::storageImageUrl($image->original_path),
            'thumbnail' => $image->thumbnail_path ? \App\Helpers\GlobalHelper::storageImageUrl($image->thumbnail_path) : null,
            'title' => $image->title,
            'alt' => $image->alt,
        ];
    }

    private function deleteImageFiles(GalleryImage $image): void
    {
        if ($image->original_path && Storage::disk('public')->exists($image->original_path)) {
            Storage::disk('public')->delete($image->original_path);
        }
        if ($image->webp_path && Storage::disk('public')->exists($image->webp_path)) {
            Storage::disk('public')->delete($image->webp_path);
        }
        if ($image->thumbnail_path && Storage::disk('public')->exists($image->thumbnail_path)) {
            Storage::disk('public')->delete($image->thumbnail_path);
        }
    }
}
