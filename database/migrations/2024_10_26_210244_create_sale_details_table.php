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
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->string('unit')->default('UN');
            $table->decimal('quantity', 10);
            $table->decimal('unitValue', 10);
            $table->decimal('unitPrice', 10);
            $table->decimal('discount', 10)->default(0);
            $table->decimal('subTotal', 10);
            $table->foreignId('sale_id')->nullable()->constrained();
            $table->foreignId('note_id')->nullable()->constrained();
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
        Schema::dropIfExists('sale_details');
    }
};
