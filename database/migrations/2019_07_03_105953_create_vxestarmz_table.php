<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVxestarmzTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vx_est_armz', function (Blueprint $table) {
            $table->increments('id');
            $table->string('erp_id',30)->nullable();
            $table->string('vxgloempfil_id',30)->nullable();
            $table->string('descricao',200)->nullable();
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
        Schema::dropIfExists('vx_est_armz');
    }
}
