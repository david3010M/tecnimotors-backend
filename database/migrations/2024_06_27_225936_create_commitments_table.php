<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commitments', function (Blueprint $table) {
            $table->id();
            $table->integer('dues'); // QUANTITY OF PAYMENTS
            $table->integer('payment_pending')->default(1); // PAYMENT NUMBER
            $table->decimal('amount'); // INITIAL PAYMENT
            $table->decimal('balance'); // REMAINING BALANCE
            $table->dateTime('payment_date'); // DATE OF PAYMENT
            $table->string('payment_type'); // TYPE OF PAYMENT
            $table->string('status')->default('Pendiente'); // STATUS OF PAYMENT
            $table->foreignId('budget_sheet_id')->constrained('budget_sheets'); // BUDGET SHEET ID
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commitments');
    }
};
