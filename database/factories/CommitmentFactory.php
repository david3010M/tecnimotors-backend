<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommitmentFactory extends Factory
{
    public function definition()
    {
        return [
            'status' => 'Pendiente',
            'sale_id' => $this->faker->randomNumber(),
        ];
    }
}
