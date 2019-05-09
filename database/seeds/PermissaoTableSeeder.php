<?php

use Illuminate\Database\Seeder;
use App\Permissao;
use App\PerfilPermissao;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PermissaoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Atualiza o cache de perfis
        Cache::forget('permissions');

        $paths = ['Central', 'Erp', 'Mobile'];

        foreach($paths as $path)
        {
            //Busca todos os arquivos de controle de permissões de acordo com o ambiente
            $arquivos = File::allFiles(base_path('app/Http/Permissions/'.$path.'/'));

            $this->update($arquivos, $path);
        }
    }

    //executa a atualização das permissões
    public function update($arquivos = [], $path)
    {
        //Inicializa array que contém somente locators válidos e funções válidas
        $availableLocators  = [];
        $availableFunctions = [];

        foreach($arquivos as $arquivo)
        {
            $basename  = str_replace(".php","", $arquivo->getBasename());
            $locator   = str_replace("Permission","Locator", $basename);
            $arquivo   = "\App\Http\Permissions\\".$path."\\".$basename;
            $class     = new $arquivo();
            $functions = $class::$functions;

            foreach($functions as $key => $value)
            {

                /*
                 * Verifica se a função já está cadastrada no banco de dados
                 *
                 * para cadastrar ou atualizar o cadastro da permissão
                 *
                 */
                $permissao = Permissao::where('function','=', $key)
                    ->where('locator','=',$locator)
                    ->where('path','=',$path)
                    ->first();

                if(!isset($permissao))
                {
                    DB::table('vx_web_permis')->insert([
                        'path'        => $path,
                        'locator'     => $locator,
                        'function'    => $key,
                        'codigo'      => $value['codigo'],
                        'titulo'      => $value['titulo'],
                        'descricao'   => $value['descricao'],
                        'controle'    => $value['controle'],
                        'prioridade'  => $value['prioridade'],
                        'superior'    => $value['superior'],
                        'created_at'  => new \DateTime(),
                        'updated_at'  => new \DateTime()
                    ]);
                }
                else
                {
                    $permissao->path        = $path;
                    $permissao->locator     = $locator;
                    $permissao->function    = $key;
                    $permissao->codigo      = $value['codigo'];
                    $permissao->titulo      = $value['titulo'];
                    $permissao->descricao   = $value['descricao'];
                    $permissao->controle    = $value['controle'];
                    $permissao->prioridade  = $value['prioridade'];
                    $permissao->superior    = $value['superior'];
                    $permissao->updated_at  = new \DateTime();
                    $permissao->save();
                }

                $availableFunctions[$locator][] = $key;
            }

            $availableLocators[] = $locator;
        }


        /**
         * Verifica quais locators cadastrados serão apagados, por não estarem na listagem obtida
         *
         */
        $locators = Permissao::whereNotIn('locator',$availableLocators)->where('path',$path)->get();

        foreach ($locators as $item)
        {
            /**
             * Apaga a permissão dos perfis vinculados
             *
             */
            PerfilPermissao::where('vxwebpermis_id','=',$item->id)->delete();

            $item->delete();
        }

        /**
         * Verifica quais functions cadastradas serão apagadas, por não estarem na listagem obtida
         *
         */
        $functions = Permissao::where('path',$path)->get();

        foreach ($functions as $item)
        {
            /**
             * Apaga a permissão dos perfis vinculados
             *
             */
            if(!in_array($item->function, $availableFunctions[$item->locator]))
            {
                PerfilPermissao::where('vxwebpermis_id','=',$item->id)->delete();

                $item->delete();
            }
        }
    }
}
