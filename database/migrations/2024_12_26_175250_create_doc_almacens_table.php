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

    protected $fillable = [
        'id',
        'date_moviment',
        'quantity',
        'comment',
        'user_id',
        'concept_mov_id',
        'product_id',
        'created_at',
    ];

    public function up()
    {
        Schema::create('doc_almacens', function (Blueprint $table) {
            $table->id();
            $table->string('sequentialnumber')->nullable();
            $table->dateTime('date_moviment')->nullable();
            $table->integer('quantity')->nullable()->default(1);
            $table->string('comment')->nullable();
            $table->foreignId('user_id')->constrained('users');
            // $table->foreignId('product_id')->constrained('products');
            $table->foreignId('concept_mov_id')->constrained('concept_movs');
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
        Schema::dropIfExists('doc_almacens');
    }
};
