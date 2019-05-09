<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Vendedor;

//mails

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator; 

class VendedorController extends Controller
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

        $vendedores = WebService::vendedores();

        $response['success']    = $success;
        $response['log']        = $log;
        $response['vendedores'] = $vendedores;
        return $response;
    }



    public function visualiza($vendedor_id)
    {
        $success = true;
        $log     = [];

        $vendedor = WebService::vendedores();

        $response['success']  = $success;
        $response['log']      = $log;
        $response['vendedor'] = $vendedor[0];
        return $response;
    }



}