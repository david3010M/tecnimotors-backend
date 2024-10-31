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
            $table->integer('numberQuota')->nullable(); // CUANTAS CUOTAS SON
            $table->decimal('price'); // PRECIO DE LA CUOTA
            $table->decimal('amount')->default(0); // LO QUE SE VA PAGANDO
            $table->decimal('balance')->nullable()->default(0); // LO QUE FALTA PAGAR
            $table->dateTime('payment_date')->nullable(); // DATE OF PAYMENT
            $table->string('payment_type'); // TYPE OF PAYMENT CONTADO O CREDITO
            $table->string('status')->default('Pendiente'); // "Pagado" o "Pendiente"
            $table->foreignId('sale_id')->constrained('sales'); // SALE ID
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
