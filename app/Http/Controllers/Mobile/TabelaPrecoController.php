<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Assinatura;
use App\EmpresaFilial;
use App\PrecoProduto;
use App\Produto;

//mails

//framework
use App\Http\Controllers\Controller;
use App\TabelaPreco;
use App\TabelaPrecoProduto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator; 
use Carbon\Carbon;
use App\Utils\Helper;


class TabelaPrecoController extends Controller
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

        $tabelas = TabelaPreco::where(function($query){
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


        $response['success']  = $success;
        $response['log']      = $log;
        $response['tabelas']  = isset($tabelas) ? $tabelas : null;
        return $response;
    }


    public function visualiza(Request $request, $tabela_preco_id, $uf)
    {
        $success = true;
        $log     = [];

        $tabela = TabelaPreco::where(function($query){
            if($this->filial !== null)
            {
                $query->where('vxgloempfil_id',$this->filial->id);
                $query->orWhere('vxgloempfil_id',null);
            }
        })->where('status','1')->where('id',$tabela_preco_id)->first();

        if(!isset($tabela))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            $itens = [];

            $precos = TabelaPrecoProduto::join('vx_glo_prod','vx_glo_prod.id','=','vx_fat_tpprod.vxgloprod_id')
                ->select('vx_fat_tpprod.*')
                ->where('vxfattabprc_id',$tabela_preco_id)
                ->where('uf',$uf)
                ->where(function ($query) use ($request){
                    if(isset($request['termo']))
                    {
                        $query->orWhereRaw('vx_glo_prod.erp_id like "%'.$request['termo'].'%"');
                        $query->orWhereRaw('vx_glo_prod.descricao like "%'.$request['termo'].'%"');
                    }
                })
                ->get();

            foreach ($precos as $preco)
            {
                $produto = Produto::find($preco->vxgloprod_id);

                if(isset($produto))
                {
                    $item = new \stdClass();
                    $item->produto_id        = $produto->id;
                    $item->produto_erp_id    = $produto->erp_id;
                    $item->produto_descricao = $produto->descricao;
                    $item->produto_unidade   = $produto->unidade_principal;
                    $item->preco_venda       = $preco->preco_venda;
                    $item->preco_maximo      = $preco->preco_maximo;
                    $item->valor_desconto    = $preco->valor_desconto;

                    $itens[] = $item;
                }
            }

            $tabela->itens = $itens;
        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['tabela']  = $tabela;
        return $response;
    }


}