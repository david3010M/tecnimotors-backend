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
            $table->foreignId('budget_sheet_id')->nullable()->constrained('budget_sheets');
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
            $table->dropForeign(['budget_sheet_id']);
            $table->dropColumn('budget_sheet_id');
        });
    }
};
