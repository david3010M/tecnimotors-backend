<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commitment>
 */
class CommitmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        /**
         * $table->id();
         * $table->integer('dues'); // QUANTITY OF PAYMENTS
         * $table->decimal('amount'); // INITIAL PAYMENT
         * $table->decimal('balance'); // REMAINING BALANCE
         * $table->dateTime('payment_date'); // DATE OF PAYMENT
         * $table->string('payment_method'); // METHOD OF PAYMENT
         * $table->string('status'); // STATUS OF PAYMENT
         * $table->foreignId('budget_sheet_id')->constrained('budget_sheets'); // BUDGET SHEET ID
         * $table->timestamps();
         * $table->softDeletes();
         */
        return [
            'dues' => $this->faker->randomNumber(2),
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'balance' => $this->faker->randomFloat(2, 0, 1000),
            'payment_date' => $this->faker->dateTime(),
//            'payment_method' => $this->faker->randomElement(['Yape', 'Plin', 'Efectivo', 'Tarjeta']),
            'payment_type' => $this->faker->randomElement(['Semanal', 'Quincenal', 'Mensual']),
            'status' => $this->faker->randomElement(['pending', 'paid']),
            'budget_sheet_id' => $this->faker->randomNumber(),
        ];
    }
}
