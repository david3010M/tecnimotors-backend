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
        Schema::create('guide_details', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->string('unit')->nullable();
            $table->string('quantity')->nullable();
            $table->string('weight')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('guide_id')->constrained();
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
        Schema::dropIfExists('guide_details');
    }
};
