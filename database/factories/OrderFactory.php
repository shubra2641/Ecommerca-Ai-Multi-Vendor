<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => 'pending',
            'total' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => 'USD',
            'shipping_address' => [
                'name' => $this->faker->name,
                'address' => $this->faker->address,
                'city' => $this->faker->city,
                'country' => $this->faker->country,
                'postal_code' => $this->faker->postcode,
            ],
            'payment_method' => 'credit_card',
            'payment_status' => 'pending',
            'items_subtotal' => $this->faker->randomFloat(2, 10, 900),
            'shipping_price' => $this->faker->randomFloat(2, 5, 50),
            'has_backorder' => false,
        ];
    }
}
