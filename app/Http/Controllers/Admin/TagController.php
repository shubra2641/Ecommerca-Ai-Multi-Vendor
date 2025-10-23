<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::orderBy('name')->paginate(50);

        return view('admin.blog.tags.index', compact('tags'));
    }

    public function store(Request $request, HtmlSanitizer $sanitizer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => 'nullable|string|max:120|unique:tags,slug',
        ]);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        // sanitize string inputs
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $data[$k] = $sanitizer->clean($v);
            }
        }
        Tag::create($data);

        return back()->with('success', __('Tag created'));
    }

    public function update(Request $request, Tag $tag, HtmlSanitizer $sanitizer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => 'required|string|max:120|unique:tags,slug,'.$tag->id,
        ]);
        // Sanitize incoming string values
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                $data[$k] = $sanitizer->clean($v);
            }
        }

        $tag->update($data);

        return back()->with('success', __('Tag updated'));
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return back()->with('success', __('Tag deleted'));
    }
}
