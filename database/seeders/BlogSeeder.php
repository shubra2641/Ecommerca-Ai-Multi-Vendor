<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure an author user exists (first admin or first user)
        $author = User::query()->first();

        // Categories
        $categoriesData = [
            ['slug' => 'news', 'name' => 'News', 'description' => 'Latest updates and announcements', 'seo_title' => 'News', 'seo_description' => 'Latest news and announcements', 'seo_tags' => 'news,updates'],
            ['slug' => 'guides', 'name' => 'Guides', 'description' => 'Step by step guides', 'seo_title' => 'Guides', 'seo_description' => 'How-to guides and tutorials', 'seo_tags' => 'guides,tutorials'],
            ['slug' => 'tips', 'name' => 'Tips', 'description' => 'Short tips', 'seo_title' => 'Tips', 'seo_description' => 'Helpful tips', 'seo_tags' => 'tips,short'],
        ];
        $categories = collect();
        foreach ($categoriesData as $c) {
            $categories->push(PostCategory::firstOrCreate(['slug' => $c['slug']], $c));
        }

        // Tags
        $tagsList = ['laravel', 'php', 'web', 'design', 'update'];
        $tags = collect();
        foreach ($tagsList as $t) {
            $tags->push(Tag::firstOrCreate(['slug' => $t], ['name' => Str::title($t)]));
        }

        // Posts (at least 4)
        $postsData = [
            [
                'slug' => 'welcome-to-our-blog',
                'title' => 'Welcome to Our Blog',
                'excerpt' => 'Introduction to the new blog section.',
                'body' => '<p>This is the first post introducing our blog section. Stay tuned!</p>',
                'seo_title' => 'Welcome Blog',
                'seo_description' => 'Introduction post to the blog',
                'seo_tags' => 'welcome,blog',
                'category_slug' => 'news',
                'image' => 'sample1.jpg',
            ],
            [
                'slug' => 'getting-started-guide',
                'title' => 'Getting Started Guide',
                'excerpt' => 'How to get started quickly.',
                'body' => '<p>Follow these steps to get started...</p>',
                'seo_title' => 'Getting Started',
                'seo_description' => 'Guide to start using the system',
                'seo_tags' => 'start,guide',
                'category_slug' => 'guides',
                'image' => 'sample2.jpg',
            ],
            [
                'slug' => 'productivity-tips',
                'title' => 'Productivity Tips',
                'excerpt' => 'Boost your productivity.',
                'body' => '<p>Use these tips to increase productivity...</p>',
                'seo_title' => 'Productivity Tips',
                'seo_description' => 'Helpful productivity tips',
                'seo_tags' => 'productivity,tips',
                'category_slug' => 'tips',
                'image' => 'sample3.jpg',
            ],
            [
                'slug' => 'weekly-update-1',
                'title' => 'Weekly Update #1',
                'excerpt' => 'Summary of this week.',
                'body' => '<p>This week we accomplished several tasks...</p>',
                'seo_title' => 'Weekly Update',
                'seo_description' => 'First weekly update',
                'seo_tags' => 'weekly,update',
                'category_slug' => 'news',
                'image' => 'sample4.jpg',
            ],
        ];

        foreach ($postsData as $pd) {
            $category = PostCategory::where('slug', $pd['category_slug'])->first();
            $post = Post::firstOrCreate(['slug' => $pd['slug']], [
                'title' => $pd['title'],
                'excerpt' => $pd['excerpt'],
                'body' => $pd['body'],
                'seo_title' => $pd['seo_title'],
                'seo_description' => $pd['seo_description'],
                'seo_tags' => $pd['seo_tags'],
                'category_id' => $category?->id,
                'user_id' => $author?->id,
                'published' => true,
                'published_at' => now(),
                'featured_image' => 'uploads/blog/' . $pd['image'],
            ]);
            // attach 2 random tags
            $randTags = $tags->random(2)->pluck('id');
            $post->tags()->syncWithoutDetaching($randTags);
        }
    }
}
