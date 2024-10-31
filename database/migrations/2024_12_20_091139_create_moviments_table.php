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
        Schema::create('moviments', function (Blueprint $table) {
            $table->id();
            $table->string('sequentialNumber')->nullable();
            $table->dateTime('paymentDate')->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->decimal('yape', 10, 2)->nullable();
            $table->decimal('deposit', 10, 2)->nullable();
            $table->decimal('cash', 10, 2)->nullable();
            $table->decimal('card', 10, 2)->nullable();
            $table->decimal('plin', 10, 2)->nullable();

            $table->string('typeDocument')->nullable();
            $table->boolean('isBankPayment')->nullable()->default(false);
            $table->string('nro_operation')->nullable();
            $table->string('numberVoucher')->nullable();
            $table->string('routeVoucher')->nullable();
            $table->string('comment')->nullable();
            $table->string('status')->nullable()->default('Generada');

            $table->foreignId('person_id')->nullable()->unsigned()->constrained('people');
            $table->foreignId('user_id')->nullable()->unsigned()->constrained('users');
            $table->foreignId('bank_id')->nullable()->unsigned()->constrained('banks');
            $table->foreignId('paymentConcept_id')->nullable()->unsigned()->constrained('concept_pays');
            $table->foreignId('budgetSheet_id')->nullable()->unsigned()->constrained('budget_sheets');
            $table->foreignId('sale_id')->nullable()->unsigned()->constrained('sales');

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
        Schema::dropIfExists('moviments');
    }
};
