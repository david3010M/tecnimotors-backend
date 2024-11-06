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
            $table->string('fullNumber')->nullable();
            $table->date('paymentDate')->nullable();
            $table->string('documentType')->nullable(); // BOLETA, FACTURA, TICKET
            $table->string('saleType')->nullable(); // NORMAL, DETRACCION
            $table->string('detractionCode')->nullable();
            $table->string('detractionPercentage')->nullable();
            $table->string('paymentType')->nullable(); // CONTADO, CREDITO
            $table->string('status')->nullable()->default('PENDIENTE');
            $table->string('status_facturado')->nullable()->default('PENDIENTE'); // PENDIENTE O ENVIADO
            $table->decimal('taxableOperation', 10)->nullable();
            $table->decimal('igv', 10)->nullable();
            $table->decimal('total', 10)->nullable();
            $table->decimal('yape', 10)->nullable();
            $table->decimal('deposit', 10)->nullable();
            $table->string('nro_operation')->nullable();
            $table->decimal('effective', 10)->nullable();
            $table->decimal('card', 10)->nullable();
            $table->decimal('plin', 10)->nullable();
            $table->boolean('isBankPayment')->nullable()->default(false);
            $table->string('numberVoucher')->nullable();
            $table->string('routeVoucher')->nullable();
            $table->string('comment')->nullable();
            $table->foreignId('person_id')->nullable()->unsigned()->constrained('people');
            $table->foreignId('budget_sheet_id')->nullable()->unsigned()->constrained('budget_sheets');
            $table->foreignId('cash_id')->nullable()->unsigned()->constrained('cashes');
            $table->foreignId('user_id')->nullable()->unsigned()->constrained('users');
            $table->foreignId('bank_id')->nullable()->unsigned()->constrained('banks');
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
