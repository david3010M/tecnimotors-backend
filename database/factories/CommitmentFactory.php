<?php

namespace Database\Factories;

use Carbon\Carbon;
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

        $typePayment = $this->faker->randomElement(['Semanal', 'Quincenal', 'Mensual']);
        $dues = $this->faker->randomNumber(1, 1);
        if ($typePayment === 'Semanal') {
            $paymentDate = Carbon::now()->addWeeks($dues);
        } elseif ($typePayment === 'Quincenal') {
            $paymentDate = Carbon::now()->addWeeks($dues * 2);
        } else {
            $paymentDate = Carbon::now()->addMonths($dues);
        }


        return [
            'dues' => $dues,
            'payment_pending' => $dues,
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'balance' => $this->faker->randomFloat(2, 0, 1000),
            'payment_date' => $paymentDate,
//            'payment_method' => $this->faker->randomElement(['Yape', 'Plin', 'Efectivo', 'Tarjeta']),
            'payment_type' => $typePayment,
//            'status' => $this->faker->randomElement(['pending', 'paid']),
            'status' => 'Pendiente',
            'budget_sheet_id' => $this->faker->randomNumber(),
        ];
    }
}
