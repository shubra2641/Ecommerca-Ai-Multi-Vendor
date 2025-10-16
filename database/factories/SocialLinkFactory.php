<?php

namespace Database\Factories;

use App\Models\SocialLink;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialLinkFactory extends Factory
{
    protected $model = SocialLink::class;

    public function definition(): array
    {
        return [
            'platform' => $this->faker->randomElement(['facebook', 'twitter', 'instagram', 'github']),
            'label' => $this->faker->word(),
            'url' => $this->faker->url(),
            'icon' => 'fas fa-link',
            'order' => 1,
            'is_active' => true,
        ];
    }
}
