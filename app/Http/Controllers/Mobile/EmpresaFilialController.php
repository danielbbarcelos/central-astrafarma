<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Assinatura;
use App\EmpresaFilial;

//mails

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator; 
use App\Utils\Helper;


class EmpresaFilialController extends Controller
{

    //construct
    public function __construct()
    {
         //
    }


    public function lista()
    {
        $success = true;
        $log     = [];

        $filiais = EmpresaFilial::orderBy('erp_id','asc')->orderBy('filial_erp_id','asc')->get();

        $response['success'] = $success;
        $response['log']     = $log;
        $response['filiais'] = $filiais;
        return $response;
    }


    public function visualiza($empresa_filial_id)
    {
        $success = true;
        $log     = [];

        $filial = EmpresaFilial::find($empresa_filial_id);

        if(!isset($filial))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['filial']  = $filial;
        return $response;
    }


}