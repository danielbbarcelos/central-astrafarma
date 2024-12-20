<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
use App\Assinatura;
use App\EmpresaFilial;
use App\Perfil;
use App\User;

//mails

//framework
use App\Http\Controllers\Controller;
use App\UserDashboard;
use App\UserEmpresaFilial;
use App\Utils\Helper;
use App\Vendedor;
use App\VexSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//packages

//extras
use Illuminate\Support\Facades\File;
use Validator;

class VexSyncController extends Controller
{

    //construct
    public function __construct()
    {
         //
    }


    //retorna array do objeto
    public function lista($situacao)
    {
        $success = true;
        $log     = [];

        if($situacao !== 'com-sucesso' and $situacao !== 'sem-sucesso' and $situacao !== 'pendentes')
        {
            $success = false;
            $log[]   = ['error' => 'Requisição inválida'];
        }
        else
        {
            $syncs = VexSync::where(function($query) use ($situacao){

                if($situacao == 'com-sucesso')
                {
                    $query->where('status','1');
                    $query->where('sucesso','1');
                }
                elseif($situacao == 'sem-sucesso')
                {
                    $query->where('status','1');
                    $query->where('sucesso','0');
                }
                elseif($situacao == 'pendentes')
                {
                    $query->where('status','0');
                    $query->where('sucesso','0');
                }

            })->orderBy('updated_at','desc')->get();
        }


        //trata as mensagens com erro no vex sync
        if($situacao == 'sem-sucesso')
        {
            foreach($syncs as $sync)
            {
                $sync->log = str_replace(["\n",'"','`',"'"],[' ', '','',''],json_decode($sync->log)->mensagem);
            }
        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['syncs']   = isset($syncs) ? $syncs : [];
        return $response;
    }


    //altera status de item
    public function alteraStatus($id)
    {
        $success = true;
        $log     = [];

        $sync = VexSync::where('id',$id)->where('sucesso','0')->first();


        if(!isset($sync))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            if((int)$sync->bloqueado == 1)
            {
                $sync->bloqueado  = '0';
                $sync->updated_at = new \DateTime();
                $sync->save();

                $log[]   = ['success' => 'Sincronização do item desbloqueada com sucesso'];
            }
            else
            {
                $sync->bloqueado  = '1';
                $sync->updated_at = new \DateTime();
                $sync->save();

                $log[]   = ['success' => 'Sincronização do item bloqueada com sucesso'];
            }
        }


        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //post para excluir um objeto
    public function excluiPost(Request $request, $id)
    {
        $success = true;
        $log     = [];

        $sync = VexSync::where('id',$id)->first();

        if(!isset($sync))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {

            $sync->delete();

            $log[]   = ['success' => 'VEX Sync excluído com sucesso'];

        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //tela para realizar download dos arquivos de log
    public function listaArquivo()
    {
        $success  = true;
        $log      = [];
        $arquivos = [];

        if(Auth::user()->type !== 'S')
        {
            $success = false;
            $log[]   = ['error' => 'Requisição inválida'];
        }
        else
        {
            $path  = storage_path('logs');

            $files = File::allFiles($path);


            foreach($files as $file)
            {
                if(strpos($file->getFilename(),'sync-central') !== false)
                {
                    $arquivos['Central VEX'][$file->getFilename()] = $file;
                }
                else if(strpos($file->getFilename(),'sync-erp') !== false)
                {
                    $arquivos['ERP'][$file->getFilename()] = $file;
                }
            }
        }

        //ordena array em ordem decrescente
        foreach($arquivos as $tipo => $itens)
        {
            krsort($itens, SORT_STRING);
            $arquivos[$tipo] = $itens;
        }

        $response['success']  = $success;
        $response['log']      = $log;
        $response['arquivos'] = $arquivos;
        return $response;
    }



    //realiza download dos arquivos de log
    public function downloadArquivo($filename)
    {
        $success  = true;
        $log      = [];
        $path     = null;


        if(Auth::user()->type !== 'S')
        {
            $success = false;
            $log[]   = ['error' => 'Requisição inválida'];
        }
        else
        {
            $path  = storage_path('logs').'/'.$filename;

            if(!File::exists($path))
            {
                $success = false;
                $log[]   = ['error' => 'Aquivo não encontrado'];
            }

        }


        $response['success']  = $success;
        $response['log']      = $log;
        $response['path']     = $path;
        return $response;
    }

}