<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRiscoColumnIntoClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_glo_cli', function (Blueprint $table) {
            $table->string('risco',1)->comment('Código de classificação de risco, utilizado para definir parâmetros de geração de pedido para o cliente')->after('saldo_devedor');
        });

        \Illuminate\Support\Facades\DB::table('vx_glo_cli')->update([
            'risco' => 'A'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vx_glo_cli', function (Blueprint $table) {
            $table->dropColumn('risco');
        });
    }
}
