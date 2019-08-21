<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Armazem;
use App\Assinatura;
use App\Cliente;
use App\EmpresaFilial;
use App\Http\Controllers\Mobile\VexSyncController;

//mails

//framework
use App\Http\Controllers\Controller;
use App\Lote;
use App\TabelaPrecoArmazem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages
use GuzzleHttp\Client;

//extras
use Illuminate\Support\Facades\Log;
use Validator;
use Carbon\Carbon;
use App\Rules\Cpf;
use App\Rules\Cnpj;
use App\Utils\Aliases;
use App\Utils\Helper;

class LoteController extends Controller
{
    protected $filial;

    //construct
    public function __construct($filialId = null)
    {
        $this->filial = isset($filialId) ? EmpresaFilial::where('filial_erp_id',$filialId)->first() : null;
    }


    //retorna array de lotes por produto
    public function lista(Request $request, $produto_id, $tabela_preco_id, $pedido_id = null)
    {
        $success = true;
        $log     = [];


        //busca os armazéns pertencentes a tabela de preço informada
        $armazens = [];

        foreach(TabelaPrecoArmazem::where('vxfattabprc_id',$tabela_preco_id)->get() as $item)
        {
            $armazens[] = $item->vxestarmz_id;
        }

        $lotes = Lote::whereIn('vxestarmz_id',$armazens)
            ->where('vxgloprod_id',$produto_id)
            ->where('saldo','>','0')
            ->where('dt_valid','!=',null)
            ->where('dt_valid','>=',Carbon::now()->addDays(1)->format('Y-m-d'))
            ->orderBy('dt_valid','asc')
            ->get();


        //retornamos o saldo do lote, com base no saldo - quantidade empenhada
        $result = [];

        foreach($lotes as $lote)
        {
            //caso pedido_id tenha sido informado, realizamos o cálculo de empenho, ignorando os itens do pedido
            if($pedido_id !== null)
            {
                $lote->empenho = \App\Http\Controllers\Central\LoteController::confirmaQuantidadeEmpenhada($lote->erp_id, $pedido_id)['empenho'];
            }

            $lote->saldo = $lote->saldo - $lote->empenho;

            if($lote->saldo > 0)
            {
                $result[] = $lote;
            }
        }

        $lotes = $result;

        $response['success']  = $success;
        $response['log']      = $log;
        $response['lotes']    = $lotes;
        return $response;
    }


    public function calculaPorItemPost(Request $request)
    {
        $success = true;
        $log     = [];

        if(!isset($request['produto_id']) or !isset($request['quantidade']) or !isset($request['produto_id']))
        {
            $success = false;
            $log[]   = ['error' => 'Produto, quantidade ou tabela de preço não informada'];
        }
        else
        {

            //busca os armazéns pertencentes a tabela de preço informada
            $armazens = [];

            foreach(TabelaPrecoArmazem::where('vxfattabprc_id',$request['tabela_id'])->get() as $item)
            {
                $armazens[] = $item->vxestarmz_id;
            }


            //gera o item de acordo com os lotes disponíveis
            $itens = [];

            $lotes = Lote::whereIn('vxestarmz_id',$armazens)
                ->where('vxgloprod_id',$request['produto_id'])
                ->where('saldo','>','0')
                ->where('dt_valid','!=',null)
                ->where('dt_valid','>=',Carbon::now()->addDays(1)->format('Y-m-d'))
                ->orderBy('dt_valid','asc')
                ->get();

            if(count($lotes) == 0)
            {
                $success = false;
                $log[]   = ['error' => 'Não há lotes disponíveis para este produto'];
            }
            else
            {
                $pendente = (int) $request['quantidade'];

                foreach ($lotes as $lote)
                {
                    $item = new \stdClass();
                    $item->produto_id      = $request['produto_id'];
                    $item->armazem_id      = $lote->vxestarmz_id;
                    $item->lote_id         = $lote->id;
                    $item->lote_erp_id     = $lote->erp_id;
                    $item->data_fabricacao = Carbon::createFromFormat('Y-m-d',$lote->dt_fabric)->format('d/m/Y');
                    $item->data_validade   = Carbon::createFromFormat('Y-m-d',$lote->dt_valid)->format('d/m/Y');

                    if((float)$lote->saldo > $pendente)
                    {
                        $item->quantidade = $pendente;

                        $pendente = 0;
                    }
                    else
                    {
                        $item->quantidade = (int)$lote->saldo;

                        $pendente = $pendente - $item->quantidade;
                    }

                    $itens[] = $item;

                    if($pendente == 0)
                    {
                        break;
                    }
                }

                if($pendente > 0)
                {
                    $success    = false;
                    $disponivel = (int) $request['quantidade'] - $pendente;
                    $log[]      = ['error' => 'Há somente '.$disponivel.' unidades disponíveis deste produto em estoque'];
                }
            }
        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['itens']   = isset($itens) ? $itens : [];
        return $response;
    }


}