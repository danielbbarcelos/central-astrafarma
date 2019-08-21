<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmpenhoColumnIntoVexestloteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_est_lote', function (Blueprint $table) {
            $table->decimal('empenho',10,2)->default(0.00)->comment('Quantidade empenhada para pedidos não sincronizados')->after('saldo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vx_est_lote', function (Blueprint $table) {
            $table->dropColumn('empenho');
        });
    }
}
