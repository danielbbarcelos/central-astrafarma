<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVxfattparmzTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vx_fat_tparmz', function (Blueprint $table) {
            $table->increments('id');
            $table->string('erp_id',30)->nullable();
            $table->integer('vxgloempfil_id')->nullable();
            $table->integer('vxfattabprc_id')->nullable();
            $table->string('vxfattabprc_erp_id',30)->nullable();
            $table->integer('vxestarmz_id')->nullable();
            $table->string('vxestarmz_erp_id',30)->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vx_fat_tparmz');
    }
}
