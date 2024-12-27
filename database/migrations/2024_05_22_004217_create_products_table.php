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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedDecimal('purchase_price', 10)->nullable();
            $table->unsignedDecimal('percentage', 10)->nullable();
            $table->unsignedDecimal('sale_price', 10)->nullable();
            $table->unsignedDecimal('stock', 10)->nullable()->default(0);
            $table->unsignedDecimal('quantity', 10)->nullable();
            $table->string('type')->nullable();

            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('brand_id')->constrained('brands');

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
        Schema::dropIfExists('products');
    }
};
