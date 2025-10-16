<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'method' => 'credit_card',
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => 'USD',
            'status' => 'pending',
            'transaction_id' => 'txn_' . Str::random(16),
            'payload' => [
                'gateway_response' => [
                    'status' => 'pending',
                    'message' => 'Payment initiated',
                ],
                'metadata' => [
                    'ip_address' => $this->faker->ipv4,
                    'user_agent' => $this->faker->userAgent,
                ],
            ],
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'failed_at' => now(),
            'failure_reason' => 'Payment declined',
        ]);
    }
}
