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
        Schema::create('attentions', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->string('correlativo')->unique();

            $table->dateTime('arrivalDate')->nullable();
            $table->dateTime('deliveryDate')->nullable();
            $table->string('observations')->nullable();
            $table->string('fuelLevel')->nullable();
            $table->decimal('km')->nullable();

            $table->decimal('totalService')->nullable();
            $table->decimal('totalProducts')->nullable();
            $table->decimal('total')->nullable();
            $table->decimal('debtAmount')->nullable()->default(0.00);
            $table->integer('percentage')->nullable()->default(0);
            $table->string('driver')->nullable();

            $table->string('routeImage')->nullable();
            $table->foreignId('worker_id')->nullable()->unsigned()->constrained('workers');
            $table->foreignId('vehicle_id')->nullable()->unsigned()->constrained('vehicles');
            // $table->foreignId('driver_id')->nullable()->unsigned()->constrained('people');

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
        Schema::dropIfExists('attentions');
    }
};
