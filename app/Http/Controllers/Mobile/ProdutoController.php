<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Assinatura;
use App\EmpresaFilial;
use App\Lote;
use App\Produto;

//mails

//framework
use App\Http\Controllers\Controller;
use App\TabelaPreco;
use App\TabelaPrecoProduto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator; 
use App\Utils\Helper;


class ProdutoController extends Controller
{
    protected $filial;

    //construct
    public function __construct($filialId = null)
    {
        $this->filial = isset($filialId) ? EmpresaFilial::where('filial_erp_id',$filialId)->first() : null;
    }


    public function lista(Request $request)
    {
        $success = true;
        $log     = [];


        $produtos = Produto::where(function($query) {
            if($this->filial !== null)
            {
                $query->where('vxgloempfil_id',$this->filial->id);
                $query->orWhere('vxgloempfil_id',null);
            }
        })->where(function ($query) use ($request){

            if(isset($request['termo']))
            {
                $query->orWhereRaw('erp_id like "%'.$request['termo'].'%"');
                $query->orWhereRaw('descricao like "%'.$request['termo'].'%"');
            }

        })->where('status','1')->orderBy('descricao','asc')->get();


        //caso a requisição tenha sido realizada com o parâmetro "termo", retornamos os saldos dos produtos
        foreach($produtos as $produto)
        {
            //retorna o saldo em estoque do produto
            $loteController = new LoteController();
            $resultado      = $loteController->saldoPorProduto($produto->id);
            $produto->saldo = $resultado['saldo'];
        }


        $response['success'] = $success;
        $response['log']     = $log;
        $response['produtos'] = $produtos;
        return $response;
    }


    public function visualiza(Request $request, $produto_id)
    {
        $success = true;
        $log     = [];

        $produto = Produto::find($produto_id);

        if(!isset($produto))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            //retorna o saldo em estoque do produto
            $loteController = new LoteController();
            $resultado      = $loteController->saldoPorProduto($produto_id);
            $produto->saldo = $resultado['saldo'];



            //retorna os preços do produto
            $tabelas = [];

            foreach(TabelaPreco::all() as $tabela)
            {
                $precos = TabelaPrecoProduto::where('vxfattabprc_id',$tabela->id)->where(function($query) use ($request){

                    //verifica se foi enviado a UF para buscar as tabelas de preços
                    if($request['uf'] !== null)
                    {
                        $query->where('uf',$request['uf']);
                    }

                })->where('vxgloprod_id',$produto_id)->orderBy('uf','asc')->get();
		
                if(count($precos) > 0)
                {
                    $tabela->precos = $precos;

                    $tabelas[] = $tabela;
                }
            }

            $produto->tabelas = $tabelas;


        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['produto'] = $produto;
        return $response;
    }


}
