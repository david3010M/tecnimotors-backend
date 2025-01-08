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
        Schema::table('doc_almacens', function (Blueprint $table) {
            $table->foreignId('attention_id')->nullable()->constrained('attentions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doc_almacens', function (Blueprint $table) {
            $table->dropColumn('attention_id');
        });
    }
};
