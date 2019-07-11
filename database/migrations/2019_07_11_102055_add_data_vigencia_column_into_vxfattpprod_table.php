<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataVigenciaColumnIntoVxfattpprodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_fat_tpprod', function (Blueprint $table) {
            $table->date('data_vigencia')->nullable()->after('vxgloprod_erp_id');
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
            $table->dropColumn('data_vigencia');
        });
    }
}
