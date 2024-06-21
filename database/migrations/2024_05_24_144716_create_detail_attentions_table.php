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
        Schema::create('detail_attentions', function (Blueprint $table) {
            $table->id();

            $table->decimal('saleprice', 10, 2);
            $table->integer('quantity')->nullable()->default(1);
            $table->string('type');
            $table->text('comment')->nullable();
            $table->string('status')->default('Generada');
            $table->date('dateRegister')->nullable();
            $table->date('dateMax')->nullable();
            $table->date('dateCurrent')->nullable();
            $table->integer('percentage')->nullable()->default(0);

            $table->foreignId('attention_id')->nullable()->unsigned()->constrained('attentions');
            $table->foreignId('worker_id')->nullable()->unsigned()->constrained('workers');
            $table->foreignId('service_id')->nullable()->unsigned()->constrained('services');
            $table->foreignId('product_id')->nullable()->unsigned()->constrained('products');

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
        Schema::dropIfExists('detail_attentions');
    }
};
