<?php

use App\Models\Attention;
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
        Schema::create('attentions', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->string('correlativo')->unique();

            $table->dateTime('arrivalDate')->nullable();
            $table->dateTime('deliveryDate')->nullable();
            $table->string('observations')->nullable();
            $table->string('fuelLevel')->nullable();
            $table->string('km')->nullable();

            $table->decimal('totalService')->nullable();
            $table->decimal('totalProducts')->nullable();
            $table->decimal('total')->nullable();
            $table->decimal('debtAmount')->nullable()->default(0.00);
            $table->integer('percentage')->nullable()->default(0);
            $table->string('driver')->nullable();
            $table->string('typeMaintenance')->default(Attention::MAINTENICE_CORRECTIVE);

            $table->string('routeImage')->nullable();
            $table->string('status')->nullable()->default('Pendiente'); // Pendiente, En proceso, Finalizada, Pagada Sin Boletear
            $table->foreignId('worker_id')->nullable()->unsigned()->constrained('workers');
            $table->foreignId('vehicle_id')->nullable()->unsigned()->constrained('vehicles');
            // $table->foreignId('driver_id')->nullable()->unsigned()->constrained('people');
            $table->foreignId('concession_id')->nullable()->unsigned()->constrained('concessions');

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
