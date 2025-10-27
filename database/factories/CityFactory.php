<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Governorate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition()
    {
        return [
            'governorate_id' => Governorate::factory(),
            'name' => $this->faker->city,
            'active' => true,
        ];
    }
}
