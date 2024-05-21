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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate');
            $table->unsignedDecimal('km');
            $table->integer('year');
            $table->string('model');
            $table->string('chasis');
            $table->string('motor');
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
