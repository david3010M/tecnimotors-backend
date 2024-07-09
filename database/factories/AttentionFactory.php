<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attention>
 */
class AttentionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'number' => $this->faker->regexify('OTRS-\d{8}'),
            'correlativo' => $this->faker->regexify('\d{8}'),
            'arrivalDate' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'deliveryDate' => $this->faker->dateTimeBetween('now', '+1 month'),
            'observations' => $this->faker->sentence(15),
            'fuelLevel' => $this->faker->numberBetween(0, 10),
            'km' => $this->faker->numberBetween(0, 100000),
            'totalService' => $this->faker->randomFloat(2, 0, 1000),
            'totalProducts' => $this->faker->randomFloat(2, 0, 1000),
            'total' => function (array $attributes) {
                return $attributes['totalService'] + $attributes['totalProducts'];
            },
            'routeImage' => $this->faker->imageUrl(),
            'vehicle_id' => $this->faker->numberBetween(1, 5),
            'worker_id' => $this->faker->numberBetween(1, 7),
        ];
    }
}
