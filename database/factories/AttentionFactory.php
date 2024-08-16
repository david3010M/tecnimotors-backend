<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttentionFactory extends Factory
{
    public function definition()
    {
        static $correlativo = 0;

        $correlativo++;
        $correlativoFormatted = str_pad($correlativo, 8, '0', STR_PAD_LEFT);

        return [
            'number' => 'OTRS-' . $correlativoFormatted,
            'correlativo' => $correlativo,
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
            'driver' => 'Driver',
        ];
    }
}
