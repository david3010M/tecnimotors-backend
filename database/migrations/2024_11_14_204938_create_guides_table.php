<?php

use App\Models\Guide;
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
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->string('full_number')->nullable();
            $table->string('date_emision')->nullable();
            $table->string('date_traslado')->nullable();
            $table->string('motive_name')->nullable();
            $table->string('cod_motive')->nullable();
            $table->string('modality')->nullable();
            $table->string('recipient_names')->nullable();
            $table->string('recipient_document')->nullable();
            $table->string('driver_fullnames')->nullable();
            $table->string('driver_names')->nullable();
            $table->string('driver_surnames')->nullable();
            $table->string('driver_document')->nullable();
            $table->string('vehicle_placa')->nullable();
            $table->string('driver_licencia')->nullable();
            $table->integer('nro_paquetes')->nullable();
            $table->boolean('transbordo')->nullable();
            $table->decimal('net_weight')->nullable();
            $table->string('ubigeo_end')->nullable();
            $table->string('address_end')->nullable();
            $table->string('ubigeo_start')->nullable();
            $table->string('address_start')->nullable();
            $table->string('observation')->nullable();
            $table->string('factura')->nullable();
            $table->string('status_facturado')->nullable()->default(Guide::STATUS_FACTURADO_PENDIENTE);
            $table->foreignId('guide_motive_id')->nullable()->constrained();
            $table->foreignId('recipient_id')->nullable()->constrained('people');
            $table->foreignId('worker_id')->nullable()->constrained('workers');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('district_id_start')->nullable()->constrained('districts');
            $table->foreignId('district_id_end')->nullable()->constrained('districts');
            $table->foreignId('branch_id')->nullable()->default(1)->constrained();
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
        Schema::dropIfExists('guides');
    }
};
