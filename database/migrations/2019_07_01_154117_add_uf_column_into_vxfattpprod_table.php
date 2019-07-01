<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUfColumnIntoVxfattpprodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_fat_tpprod', function (Blueprint $table) {
            $table->string('uf',2)->nullable()->after('fator');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vx_fat_tpprod', function (Blueprint $table) {
            $table->dropColumn('uf');
        });
    }
}
