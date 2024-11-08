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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();
            $table->string('fullNumber')->nullable();
            $table->string('documentType')->nullable();
            $table->date('date')->nullable();
            $table->string('comment')->nullable();
            $table->string('company')->default('TECNIMOTORS');
            $table->decimal('discount', 10)->default(0);
            $table->decimal('totalCreditNote', 10)->nullable();
            $table->decimal('totalDocumentReference', 10)->nullable();
            $table->string('status');
            $table->foreignId('note_reason_id')->nullable()->constrained('note_reasons');
            $table->foreignId('sale_id')->nullable()->constrained('sales'); //ref
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('cash_id')->nullable()->constrained('cashes');
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
        Schema::dropIfExists('notes');
    }
};
