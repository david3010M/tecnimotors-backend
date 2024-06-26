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
        Schema::create('specialty_people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialty_id')->nullable()->unsigned()->constrained('specialties');
            $table->foreignId('worker_id')->nullable()->unsigned()->constrained('workers');
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
        Schema::dropIfExists('specialty_people');
    }
};
