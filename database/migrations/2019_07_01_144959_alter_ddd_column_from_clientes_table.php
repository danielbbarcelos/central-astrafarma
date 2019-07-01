<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDddColumnFromClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement(' ALTER TABLE vx_glo_cli MODIFY ddd VARCHAR(3) DEFAULT NULL ;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::statement(' ALTER TABLE vx_glo_cli MODIFY ddd VARCHAR(2) DEFAULT NULL ;');
    }
}
