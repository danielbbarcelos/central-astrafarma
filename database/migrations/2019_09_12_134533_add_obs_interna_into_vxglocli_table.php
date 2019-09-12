<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddObsInternaIntoVxglocliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_glo_cli', function (Blueprint $table) {
            $table->text('obs_interna')->nullable()->after('obs_nota');
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
            $table->dropColumn('obs_interna');
        });
    }
}
