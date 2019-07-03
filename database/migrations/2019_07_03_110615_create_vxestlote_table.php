<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVxestloteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vx_est_lote', function (Blueprint $table) {
            $table->increments('id');
            $table->string('erp_id',30)->nullable()->comment('Cód. ERP do armazém + Cód. ERP do lote');
            $table->integer('vxgloempfil_id')->nullable();
            $table->integer('vxestarmz_id')->nullable();
            $table->string('vxestarmz_erp_id',30)->nullable();
            $table->integer('vxgloprod_id')->nullable();
            $table->string('vxgloprod_erp_id',30)->nullable();
            $table->date('dt_fabric')->nullable()->comment('Data de fabricação');
            $table->date('dt_valid')->nullable()->comment('Data de validade');
            $table->decimal('quant_ori',10,2)->default(0.00)->comment('Quantidade de origem');
            $table->decimal('saldo',10,2)->default(0.00)->comment('Saldo em estoque');
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
        Schema::dropIfExists('vx_est_lote');
    }
}
