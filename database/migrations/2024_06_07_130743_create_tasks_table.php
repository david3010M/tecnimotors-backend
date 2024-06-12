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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('status')->default('hacer')->comment('hacer, curso, listo');
//            $table->integer('percentage')->default(0);
            $table->date('registerDate')->default(now());
//            $table->date('dateStart')->nullable();
            $table->date('limitDate')->nullable();
            $table->foreignId('worker_id')->constrained('workers');
            $table->foreignId('detail_attentions_id')->constrained('detail_attentions');
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
        Schema::dropIfExists('tasks');
    }
};
