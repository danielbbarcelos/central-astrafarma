<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsIntoVexSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_glo_sync', function (Blueprint $table) {
            $table->integer('tentativa')->default(0)->comment('Quantidade de tentativas realizadas')->after('sucesso');
            $table->boolean('bloqueado')->default(0)->comment('0->execução liberada, 1->bloqueado para execução')->after('sucesso');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vx_glo_sync', function (Blueprint $table) {
            $table->dropColumn('tentativa');
            $table->dropColumn('bloqueado');
        });
    }
}
