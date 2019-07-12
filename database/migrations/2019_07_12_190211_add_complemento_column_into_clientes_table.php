<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddComplementoColumnIntoClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_glo_cli', function (Blueprint $table) {
            $table->string('complemento',50)->nullable()->after('endereco');
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
            $table->dropColumn('complemento');
        });
    }
}
