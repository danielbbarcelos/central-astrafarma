<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Permissao;
use App\PerfilPermissao;

class PerfilPermissaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Apaga todas as permissões do admin para associá-lo novamente
         *
         */
        PerfilPermissao::where('vxwebperfil_id','=','1')->delete();

        /*
         * Busca todas as permissões cadastradas
         *
         */
        $permissoes = Permissao::all();

        foreach ($permissoes as $permissao)
        {

            DB::table('vx_web_ppermi')->insert([
                'vxwebperfil_id' => '1',
                'vxwebpermis_id' => $permissao->id,
                'created_at'     => new \DateTime(),
                'updated_at'     => new \DateTime()
            ]);

        }
    }
}
