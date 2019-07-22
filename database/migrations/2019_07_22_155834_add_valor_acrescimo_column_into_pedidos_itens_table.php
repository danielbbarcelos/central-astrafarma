<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValorAcrescimoColumnIntoPedidosItensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_fat_ipvend', function (Blueprint $table) {
            $table->decimal('valor_acrescimo',10,2)->default(0.00)->nullable()->after('valor_desconto');
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
            $table->dropColumn('valor_acrescimo');
        });
    }
}
