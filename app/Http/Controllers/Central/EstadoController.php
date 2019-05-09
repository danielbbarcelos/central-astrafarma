<?php 

namespace App\Http\Controllers\Central;

use App\Cidade;
use App\Estado;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class EstadoController extends Controller
{

    //construct
    public function __construct()
    {
         //
    }


    //retorna cidades através do estado selecionado
    public function buscaCidades($uf)
    {
        $success = true;
        $log     = '';
        $result  = null;

        $estado = Estado::where('uf',$uf)->first();

        if(!isset($estado))
        {
            $success = false;
            $log     = 'UF inválido';
        }
        else
        {
            $result = new \stdClass();

            $result->estado  = $estado;

            $result->cidades = Cidade::where('vxgloestado_id',$estado->id)->orderBy('nome','asc')->get();
        }


        $response['success']    = $success;
        $response['log']        = $log;
        $response['result']     = $result;
        return $response;
    }

}