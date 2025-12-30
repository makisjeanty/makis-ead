<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => Order::generateOrderNumber(),
            'total' => fake()->randomFloat(2, 29.90, 299.90),
            'status' => Order::STATUS_PENDING,
            'payment_method' => fake()->randomElement(['mercadopago', 'stripe', 'wallet']),
            'metadata' => [],
        ];
    }
    
    public function pending()
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_PENDING,
        ]);
    }
    
    public function paid()
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_PAID,
        ]);
    }
    
    public function failed()
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_FAILED,
        ]);
    }
}
