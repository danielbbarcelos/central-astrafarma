<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataNfColumnIntoVxfatipvendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_fat_ipvend', function (Blueprint $table) {
            $table->date('data_nf')->nullable()->after('serienf');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vx_fat_ipvend', function (Blueprint $table) {
            $table->dropColumn('data_nf');
        });
    }
}
