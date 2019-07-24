<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVxFatRiscoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vx_fat_risco', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo',1)->comment('A, B, C, D ou E');
            $table->decimal('percentual_desconto')->default(0.00)->comment('Percentual de desconto máximo permitido para o cliente que contém a classificação');
            $table->timestamps();
        });

        $risco = new \App\Risco();
        $risco->codigo     = 'A';
        $risco->percentual_desconto = 9.00;
        $risco->created_at = new \DateTime();
        $risco->updated_at = new \DateTime();
        $risco->save();

        $risco = new \App\Risco();
        $risco->codigo     = 'B';
        $risco->percentual_desconto = 8.00;
        $risco->created_at = new \DateTime();
        $risco->updated_at = new \DateTime();
        $risco->save();

        $risco = new \App\Risco();
        $risco->codigo     = 'C';
        $risco->percentual_desconto = 7.00;
        $risco->created_at = new \DateTime();
        $risco->updated_at = new \DateTime();
        $risco->save();

        $risco = new \App\Risco();
        $risco->codigo     = 'D';
        $risco->percentual_desconto = 5.00;
        $risco->created_at = new \DateTime();
        $risco->updated_at = new \DateTime();
        $risco->save();

        $risco = new \App\Risco();
        $risco->codigo     = 'E';
        $risco->percentual_desconto = 0.00;
        $risco->created_at = new \DateTime();
        $risco->updated_at = new \DateTime();
        $risco->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vx_fat_risco');
    }
}
