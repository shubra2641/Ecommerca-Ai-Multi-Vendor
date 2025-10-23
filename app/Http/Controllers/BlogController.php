<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;

class BlogController extends Controller
{
    public function index()
    {
        $locale = app()->getLocale();
        $page = request('page', 1);
        $posts = cache()->remember("blog.index.{$locale}.{$page}", 300, function () {
            return Post::where('published', true)->orderByDesc('published_at')->paginate(10);
        });

        return view('front.blog.index', compact('posts'));
    }

    public function show($slug)
    {
        $locale = app()->getLocale();
        $cacheKey = "blog.post.{$locale}.{$slug}";
        $post = cache()->remember($cacheKey, 600, function () use ($slug, $locale) {
            return Post::where(function ($q) use ($slug, $locale): void {
                $q->where('slug', $slug)
                    ->orWhere("slug_translations->{$locale}", $slug);
            })->where('published', true)->firstOrFail();
        });
        $related = cache()->remember(
            "blog.post.{$locale}.{$slug}.related",
            600,
            fn () => Post::where('published', true)
                ->where('id', '!=', $post->id)
                ->latest()
                ->take(4)
                ->get()
        );

        return view('front.blog.show', compact('post', 'related'));
    }

    public function category($slug)
    {
        $locale = app()->getLocale();
        $category = cache()->remember(
            "blog.cat.{$locale}.{$slug}",
            600,
            fn () => PostCategory::where('slug', $slug)
                ->orWhere("slug_translations->{$locale}", $slug)
                ->firstOrFail()
        );
        $posts = Post::where('published', true)
            ->where('category_id', $category->id)
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('front.blog.category', compact('category', 'posts'));
    }

    public function tag($slug)
    {
        $locale = app()->getLocale();
        $tag = cache()->remember(
            "blog.tag.{$locale}.{$slug}",
            600,
            fn () => Tag::where('slug', $slug)
                ->orWhere("slug_translations->{$locale}", $slug)
                ->firstOrFail()
        );
        $posts = $tag->posts()
            ->where('published', true)
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('front.blog.tag', compact('tag', 'posts'));
    }
}
