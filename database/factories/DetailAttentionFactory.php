<?php

namespace Database\Factories;

use App\Models\Attention;
use App\Models\Product;
use App\Models\Service;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetailAttention>
 */
class DetailAttentionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'quantity' => $this->faker->numberBetween(1, 10),
            'type' => $this->faker->randomElement(['Service', 'Product']),
            'comment' => $this->faker->sentence(15),
            'status' => 'Generada',
            'dateRegister' => $this->faker->date(),
            'dateMax' => $this->faker->date(),
            'worker_id' => function (array $attributes) {
                return $attributes['type'] === 'Service' ? $this->faker->numberBetween(1, Worker::count()) : null;
            },
            'product_id' => function (array $attributes) {
                return $attributes['type'] === 'Product' ? $this->faker->numberBetween(1, Product::count()) : null;
            },
            'service_id' => function (array $attributes) {
                return $attributes['type'] === 'Service' ? $this->faker->numberBetween(1, Service::count()) : null;
            },
            'saleprice' => function (array $attributes) {
                return $attributes['type'] === 'Service' ? Service::find($attributes['service_id'])->saleprice : (Product::find($attributes['product_id'])->sale_price) * $attributes['quantity'];
            },
            'attention_id' => Attention::factory(),
        ];
    }
}
