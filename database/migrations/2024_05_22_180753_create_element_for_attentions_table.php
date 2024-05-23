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
        Schema::create('element_for_attentions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('element_id')->nullable()->unsigned()->constrained('elements');
            $table->foreignId('attention_id')->nullable()->unsigned()->constrained('attentions');
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
        Schema::dropIfExists('element_for_attentions');
    }
};
