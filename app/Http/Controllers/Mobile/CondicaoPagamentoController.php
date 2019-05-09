<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Assinatura;
use App\CondicaoPagamento;

//mails

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator; 
use App\Utils\Helper;


class CondicaoPagamentoController extends Controller
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

        $condicoes = CondicaoPagamento::all();

        $response['success']   = $success;
        $response['log']       = $log;
        $response['condicoes'] = $condicoes;
        return $response;
    }


}