<?php
namespace App\Http\Controllers\Central;


//framework
use App\Http\Controllers\Controller;
use App\Utils\Aliases;
use Illuminate\Http\Request;

//packages
use GuzzleHttp\Client;

//extras
use Illuminate\Support\Facades\Auth;
use Validator;


class MigracaoController extends Controller
{

    public function __construct()
    {
        //
    }
    

    //tela para formulário de migração de dados
    public function index()
    {
        $success = true;
        $log     = [];

        if(Auth::user()->type !== 'S')
        {
            $success = false;
            $log[]   = ['error' => 'Requisição inválida'];
        }

        $response['success']  = $success;
        $response['log']      = $log;
        return $response;
    }


    //realiza migração
    public function migracaoPost(Request $request)
    {
        $success = true;
        $log     = [];


        if(Auth::user()->type !== 'S')
        {
            $success = false;
            $log[]   = ['error' => 'Requisição inválida'];
        }
        else if(!env('DATA_MIGRATION_MD5') == md5($request['password']))
        {
            $success = false;
            $log[]   = ['error' => 'A senha está incorreta'];
        }
        else
        {
            $uri = $request['uri'] !== null ? $request['uri'] : 'all';

            $controller = Aliases::migracaoControllerByTable($request['tabela']);
            $result     = $controller::migracao($uri);

            if($result['success'] == true)
            {
                $log[] = ['success' => $result['log']];
            }
            else
            {
                $success = false;
                $log[]   = ['error' => $result['log']];
            }
        }

        $response['success']  = $success;
        $response['log']      = $log;
        return $response;
    }


}
