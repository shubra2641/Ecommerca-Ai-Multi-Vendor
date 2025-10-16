<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(5);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(4),
            'excerpt' => $this->faker->sentence(12),
            'body' => '<p>' . implode('</p><p>', $this->faker->paragraphs(4)) . '</p>',
            'seo_title' => $title,
            'seo_description' => $this->faker->sentence(15),
            'seo_tags' => 'demo,test',
            'published' => true,
            'published_at' => now(),
        ];
    }
}
