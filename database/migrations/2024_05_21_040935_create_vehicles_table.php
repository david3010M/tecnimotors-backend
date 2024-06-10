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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate');
            $table->unsignedDecimal('km');
            $table->integer('year')->nullable();
            $table->string('model')->nullable();
            $table->string('chasis')->nullable();
            $table->string('motor')->nullable();
            $table->string('codeBin')->unique()->nullable();
            $table->foreignId('person_id')->constrained('people');
            $table->foreignId('typeVehicle_id')->constrained('type_vehicles');
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
        Schema::dropIfExists('vehicles');
    }
};
