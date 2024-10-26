<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use function Webmozart\Assert\Tests\StaticAnalysis\string;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->date('paymentDate')->nullable();
            $table->string('documentType')->nullable(); // BOLETA, FACTURA, TICKET
            $table->string('saleType')->nullable(); // NORMAL, DETRACCION
            $table->string('detractionCode')->nullable();
            $table->string('detractionPercentage')->nullable();
            $table->string('paymentType')->nullable(); // CONTADO, CREDITO
            $table->string('status')->nullable()->default('Pendiente');

            $table->foreignId('person_id')->nullable()->unsigned()->constrained('people');
            $table->foreignId('budget_sheet_id')->nullable()->unsigned()->constrained('budget_sheets');
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
        Schema::dropIfExists('sales');
    }
};
