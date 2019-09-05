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
            $table->text('obs_nota')->nullable()->after('saldo_devedor');
            $table->boolean('envia_boleto')->default(0)->comment('0->não envia, 1->envia boleto')->after('saldo_devedor');
            $table->boolean('contribuinte')->default(0)->comment('0->não contribuinte, 1->contribuinte')->after('cnpj_cpf');
            $table->string('insc_estadual',50)->nullable()->after('cnpj_cpf');
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
            $table->dropColumn('obs_nota');
            $table->dropColumn('envia_boleto');
            $table->dropColumn('contribuinte');
            $table->dropColumn('insc_estadual');
        });
    }
}
