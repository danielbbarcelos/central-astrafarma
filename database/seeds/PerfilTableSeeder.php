<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Perfil;

class PerfilTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $perfil = Perfil::where('nome','=','Administrador')->first();

        if(!isset($perfil))
        {
            DB::table('vx_web_perfil')->insert([
                'nome'       => 'Administrador',
                'descricao'  => 'Acesso total ao sistema',
                'status'     => '1',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime()
            ]);
        }
    }
}
