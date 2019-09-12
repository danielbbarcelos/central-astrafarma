<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailsColumnsIntoVxglocliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_glo_cli', function (Blueprint $table) {
            $table->string('email_fin',100)->nullable()->after('email');
        });

        Schema::table('vx_glo_cli', function (Blueprint $table) {
            $table->string('email_con',100)->nullable()->after('email');
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
            $table->dropColumn('email_fin');
            $table->dropColumn('email_con');
        });
    }
}
