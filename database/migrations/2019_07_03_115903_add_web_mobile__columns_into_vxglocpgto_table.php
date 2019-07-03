<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWebMobileColumnsIntoVxglocpgtoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_glo_cpgto', function (Blueprint $table) {
            $table->boolean('web')->default(0)->comment('0->inativo, 1->ativo')->after('descricao');
        });
        Schema::table('vx_glo_cpgto', function (Blueprint $table) {
            $table->boolean('mobile')->default(0)->comment('0->inativo, 1->ativo')->after('web');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vx_glo_cpgto', function (Blueprint $table) {
            $table->dropColumn('web');
            $table->dropColumn('mobile');
        });
    }
}
