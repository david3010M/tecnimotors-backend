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
        Schema::create('route_images', function (Blueprint $table) {

            $table->id();
            $table->string('route');
            $table->foreignId('attention_id')->nullable()->unsigned()->constrained('attentions');
            $table->foreignId('task_id')->nullable()->unsigned()->constrained('tasks');
            $table->foreignId('concession_id')->nullable()->unsigned()->constrained('concessions');
            $table->foreignId('product_id')->nullable()->unsigned()->constrained('products');
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
        Schema::dropIfExists('route_images');
    }
};
