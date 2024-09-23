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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->date('startDate')->nullable();
            $table->date('birthDate')->nullable();
            $table->boolean('state')->nullable()->default(true);

            $table->string('occupation', 250)->nullable()->nullable();
            $table->foreignId('ocupation_id')->nullable()->unsigned()->constrained('ocupations');
            $table->foreignId('person_id')->nullable()->unsigned()->constrained('people');
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
        Schema::dropIfExists('workers');
    }
};
