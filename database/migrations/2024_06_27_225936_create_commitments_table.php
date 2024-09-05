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
//            $table->integer('payments'); // LO QUE SE VA PAGANDO
            $table->decimal('balance')->nullable()->default(0); // LO QUE FALTA PAGAR
            $table->dateTime('payment_date')->nullable(); // DATE OF PAYMENT
//            $table->string('payment_type'); // TYPE OF PAYMENT
            $table->string('status')->default('Pendiente'); // "Pagado" o "Pendiente"
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
