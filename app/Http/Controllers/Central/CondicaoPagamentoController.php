<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
use App\CondicaoPagamento;
use App\Produto;

//mails

//framework
use App\Http\Controllers\Controller;
use App\TabelaPreco;
use App\TabelaPrecoProduto;
use Illuminate\Http\Request;

//packages

//extras
use Illuminate\Support\Facades\Auth;
use Validator;


class CondicaoPagamentoController extends Controller
{

    protected $empfilId;

    //construct
    public function __construct()
    {
        $this->empfilId = Auth::user()->userEmpresaFilial->empfil->id;
    }


    //retorna array do objeto
    public function lista()
    {
        $success = true;
        $log     = [];

        $condicoes = CondicaoPagamento::where(function($query){

            $query->where('vxgloempfil_id',$this->empfilId);
            $query->orWhere('vxgloempfil_id','=',null);

        })->where('web','1')->orderBy('descricao', 'asc')->get();

        $response['success']   = $success;
        $response['log']       = $log;
        $response['condicoes'] = $condicoes;
        return $response;
    }



}