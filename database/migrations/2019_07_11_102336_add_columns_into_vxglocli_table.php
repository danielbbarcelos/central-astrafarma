<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsIntoVxglocliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_glo_cli', function (Blueprint $table) {
            $table->string('vxfatvend_erp_id',30)->nullable()->after('vxgloempfil_id');
        });

        Schema::table('vx_glo_cli', function (Blueprint $table) {
            $table->decimal('limite_credito',10,2)->default(0.00)->after('email');
        });

        Schema::table('vx_glo_cli', function (Blueprint $table) {
            $table->decimal('saldo_devedor',10,2)->default(0.00)->after('limite_credito');
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
            $table->dropColumn('vxfatvend_erp_id');
            $table->dropColumn('limite_credito');
            $table->dropColumn('saldo_devedor');
        });
    }
}
