<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    protected $model = Language::class;

    public function definition(): array
    {
        $code = $this->faker->unique()->lexify('??');

        return [
            'name' => ucfirst($code),
            'code' => strtolower($code),
            'flag' => null,
            'is_default' => false,
            'is_active' => true,
        ];
    }
}
