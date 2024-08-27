<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budget_sheets', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->string('paymentType')->nullable();
            $table->decimal('totalService', 10, 2)->nullable();
            $table->decimal('totalProducts', 10, 2)->nullable();
            $table->decimal('debtAmount', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('igv', 10, 2)->nullable();
            $table->string('status')->nullable()->default('Pendiente');
            $table->foreignId('attention_id')->nullable()->unsigned()->constrained('attentions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('budget_sheets');
    }
};
