<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFabricanteAndQtdeMinimaColumnsIntoVxgloprodTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_glo_prod', function (Blueprint $table) {
            $table->string('fabricante',200)->nullable()->after('preco_venda');
            $table->integer('qtde_minima')->default(0)->after('preco_venda');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vx_glo_prod', function (Blueprint $table) {
            $table->dropColumn('fabricante');
            $table->dropColumn('qtde_minima');
        });
    }
}
