<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsIntoVxfatipvendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_fat_pvenda', function (Blueprint $table) {
            $table->dropColumn('vxfattabprc_erp_id');
        });

        Schema::table('vx_fat_ipvend', function (Blueprint $table) {
            $table->string('vxfattabprc_erp_id',30)->nullable()->after('vxgloprod_erp_id');
        });

        Schema::table('vx_fat_ipvend', function (Blueprint $table) {
            $table->string('vxestarmz_erp_id',30)->nullable()->after('vxfattabprc_erp_id');
        });

        Schema::table('vx_fat_ipvend', function (Blueprint $table) {
            $table->string('vxestlote_erp_id',30)->nullable()->after('vxestarmz_erp_id');
        });

        Schema::table('vx_fat_ipvend', function (Blueprint $table) {
            $table->string('alerta_estoque',40)->nullable()->after('quantidade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vx_fat_pvenda', function (Blueprint $table) {
            $table->string('vxfattabprc_erp_id',30)->nullable()->after('vxglocpgto_erp_id');
        });

        Schema::table('vx_fat_ipvend', function (Blueprint $table) {
            $table->dropColumn('vxfattabprc_erp_id');
            $table->dropColumn('vxestarmz_erp_id');
            $table->dropColumn('vxestlote_erp_id');
            $table->dropColumn('alerta_estoque');
        });
    }
}
