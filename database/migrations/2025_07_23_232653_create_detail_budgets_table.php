<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('detail_budgets', function (Blueprint $table) {
            $table->id();
             $table->decimal('saleprice', 10, 2);
            $table->integer('quantity')->nullable()->default(1);
            $table->string('type');
            $table->text('comment')->nullable();
            $table->string('status')->default('Generada');
            $table->date('dateRegister')->nullable();
            $table->date('dateMax')->nullable();
            $table->date('dateCurrent')->nullable();
            $table->integer('percentage')->nullable()->default(0);
            $table->integer('period')->default(0)->nullable();
            $table->foreignId('budget_sheet_id')->nullable()->unsigned()->constrained('budget_sheets');
            $table->foreignId('worker_id')->nullable()->unsigned()->constrained('workers');
            $table->foreignId('service_id')->nullable()->unsigned()->constrained('services');
            $table->foreignId('product_id')->nullable()->unsigned()->constrained('products');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_budgets');
    }
};