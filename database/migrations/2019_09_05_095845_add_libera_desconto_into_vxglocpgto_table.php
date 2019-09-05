<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLiberaDescontoIntoVxglocpgtoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_glo_cpgto', function (Blueprint $table) {
            $table->boolean('libera_desconto')->default(0)->comment('0->não libera desconto, 1->libera desconto máximo (risco A)')->after('descricao');
        });

        DB::table('vx_glo_cpgto')->where('erp_id','002')->orWhere('erp_id','001')->update([
            'libera_desconto' => '1'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vx_glo_cpgto', function (Blueprint $table) {
            $table->dropColumn('libera_desconto');
        });
    }
}
