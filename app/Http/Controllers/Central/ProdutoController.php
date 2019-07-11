<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
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


class ProdutoController extends Controller
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

        $produtos = Produto::where(function($query){

            $query->where('vxgloempfil_id',$this->empfilId);
            $query->orWhere('vxgloempfil_id','=',null);

        })->orderBy('descricao', 'asc')->get();

        $response['success']  = $success;
        $response['log']      = $log;
        $response['produtos'] = $produtos;
        return $response;
    }


    //chamada da tela para visualizar um objeto
    public function visualiza($produto_id)
    {
        $success = true;
        $log     = [];

        $produto = Produto::where('id',$produto_id)->where(function($query){

            $query->where('vxgloempfil_id',$this->empfilId);
            $query->orWhere('vxgloempfil_id','=',null);

        })->orderBy('descricao', 'asc')->first();


        if(!isset($produto))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            $tabelas = [];

            $tabelaPrecoProduto = TabelaPrecoProduto::where('vxgloprod_erp_id',$produto->erp_id)
                ->orderBy('vxfattabprc_erp_id','asc')
                ->orderBy('data_vigencia','desc')
                ->orderBy('uf','asc')
                ->get();

            foreach($tabelaPrecoProduto as $item)
            {
                $tabela = TabelaPreco::find($item->vxfattabprc_id);

                if(isset($tabela))
                {
                    $tabela->data_vigencia  = $item->data_vigencia;
                    $tabela->uf             = $item->uf;
                    $tabela->preco_venda    = $item->preco_venda;
                    $tabela->preco_maximo   = $item->preco_maximo;
                    $tabela->valor_desconto = $item->valor_desconto;
                    $tabela->fator          = $item->fator;

                    $tabelas[] = $tabela;
                }

            }
        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['produto'] = $produto;
        $response['tabelas'] = isset($tabelas) ? $tabelas : [];
        return $response;
    }


}