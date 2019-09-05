<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsIntoVxfatvendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_fat_vend', function (Blueprint $table) {
            $table->string('fone',200)->nullable()->after('cpf');
            $table->string('email',200)->nullable()->after('cpf');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vx_fat_vend', function (Blueprint $table) {
            $table->dropColumn('fone');
            $table->dropColumn('email');
        });
    }
}
