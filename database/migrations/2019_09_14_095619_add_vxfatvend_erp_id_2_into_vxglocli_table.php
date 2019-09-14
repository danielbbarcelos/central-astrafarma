<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVxfatvendErpId2IntoVxglocliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_glo_cli', function (Blueprint $table) {
            $table->renameColumn('vxfatvend_erp_id','vxfatvend_erp_id_1');
        });

        Schema::table('vx_glo_cli', function (Blueprint $table) {
            $table->string('vxfatvend_erp_id_2',30)->nullable()->after('vxfatvend_erp_id_1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vx_glo_cli', function (Blueprint $table) {
            $table->renameColumn('vxfatvend_erp_id_1','vxfatvend_erp_id');
            $table->dropColumn('vxfatvend_erp_id_2');
        });
    }
}
