<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Assinatura;
use App\Produto;

//mails

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator; 
use App\Utils\Helper;


class ProdutoController extends Controller
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

        $produtos = Produto::orderBy('descricao','asc')->get();

        $response['success'] = $success;
        $response['log']     = $log;
        $response['produtos'] = $produtos;
        return $response;
    }


    public function visualiza($produto_id)
    {
        $success = true;
        $log     = [];

        $produto = Produto::find($produto_id);

        if(!isset($produto))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['produto'] = $produto;
        return $response;
    }


}