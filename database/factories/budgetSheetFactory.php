<?php

namespace Database\Factories;

use App\Models\Attention;
use Illuminate\Database\Eloquent\Factories\Factory;

class budgetSheetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'number' => $this->faker->regexify('PRES-\d{8}'),
            'paymentType' => $this->faker->randomElement(['Contado', 'Crédito']),
            'totalService' => $this->faker->randomFloat(2, 0, 1000),
            'totalProducts' => $this->faker->randomFloat(2, 0, 1000),
            'discount' => $this->faker->randomFloat(2, 0, 20),
            'debtAmount' => $this->faker->randomFloat(2, 0, 1000),
            'subtotal' => function (array $attributes) {
                return $attributes['totalService'] + $attributes['totalProducts'];
            },
            'igv' => function (array $attributes) {
                return $attributes['subtotal'] * 0.18;
            },
            'total' => function (array $attributes) {
                return $attributes['totalService'] + $attributes['totalProducts'] - $attributes['discount'] + $attributes['igv'];
            },
            'attention_id' => Attention::factory(),
        ];
    }
}
