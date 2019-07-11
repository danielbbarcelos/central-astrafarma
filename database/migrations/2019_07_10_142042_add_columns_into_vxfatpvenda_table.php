<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsIntoVxfatpvendaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vx_fat_pvenda', function (Blueprint $table) {
            $table->string('status_entrega',1)->nullable()->comment('1->sem programação, 2->programado, 3->pagamento')->after('data_entrega');
            $table->text('obs_interna')->nullable()->after('observacao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vx_fat_pvenda', function (Blueprint $table) {
            $table->dropColumn('status_entrega');
            $table->dropColumn('obs_interna');
        });
    }
}
