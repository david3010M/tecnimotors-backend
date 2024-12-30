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
        Schema::create('docalmacen_details', function (Blueprint $table) {
            $table->id();
            $table->string('sequentialnumber')->nullable();
            $table->integer('quantity')->nullable()->default(1);
            $table->string('comment')->nullable();

            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('doc_almacen_id')->constrained('doc_almacens');
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
        Schema::dropIfExists('docalmacen_details');
    }
};
