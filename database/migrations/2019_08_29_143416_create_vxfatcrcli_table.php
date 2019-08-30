<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVxfatcrcliTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vx_fat_crcli', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vxgloempfil_id')->nullable();
            $table->integer('vxglocli_id')->nullable();
            $table->string('vxglocli_erp_id',30)->nullable();
            $table->integer('vxfatpvenda_id')->nullable();
            $table->string('vxfatpvenda_erp_id',30)->nullable();
            $table->decimal('saldo_devedor',10,2)->nullable()->comment('Saldo devedor apenas referente ao pedido');
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
        Schema::dropIfExists('vx_fat_crcli');
    }
}
