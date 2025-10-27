<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Database\Eloquent\Factories\Factory;

class GovernorateFactory extends Factory
{
    protected $model = Governorate::class;

    public function definition()
    {
        return [
            'country_id' => Country::factory(),
            'name' => $this->faker->state,
            'active' => true,
        ];
    }
}
