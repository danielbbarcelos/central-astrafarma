<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Assinatura;
use App\Cliente;
use App\CondicaoPagamento;
use App\EmpresaFilial;
use App\PedidoVenda;
use App\PedidoItem;
use App\PrecoProduto;
use App\Produto;
use App\TabelaPrecoProduto;
use App\Vendedor;
use App\Http\Controllers\Mobile\VexSyncController;
use App\Http\Controllers\Erp\VexSyncController as ErpVexSync;

//mails

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages
use GuzzleHttp\Client;

//extras
use Validator; 
use Carbon\Carbon;
use App\Utils\Helper;

class PedidoVendaController extends Controller
{
    protected $filial;

    //construct
    public function __construct($filialId = null)
    {
         $this->filial = isset($filialId) ? EmpresaFilial::where('filial_erp_id',$filialId)->first() : null;
    }


    public function lista()
    {
        $success = true;
        $log     = [];


        $pedidos = PedidoVenda::where(function($query){
            if($this->filial !== null)
            {
                $query->where('vxgloempfil_id',$this->filial->id);
                $query->orWhere('vxgloempfil_id',null);
            }
        })->orderBy('created_at','desc')->get();


        foreach($pedidos as $pedido)
        {
            $pedido->condicao_pagamento = CondicaoPagamento::where('erp_id',$pedido->vxglocpgto_erp_id)->first()->descricao;
            $pedido->tabela_preco = PrecoProduto::where('erp_id',$pedido->vxfattabprc_erp_id)->first()->descricao;
            $pedido->data_pedido  = Carbon::createFromFormat('Y-m-d H:i:s',$pedido->created_at)->format('d/m/Y');
            $pedido->hora_pedido  = Carbon::createFromFormat('Y-m-d H:i:s',$pedido->created_at)->format('H:i:s');
            $pedido->data_entrega = $pedido->data_entrega !== null ? Carbon::createFromFormat('Y-m-d',$pedido->data_entrega)->format('d/m/Y') : null;
            $pedido->cliente_data = json_decode($pedido->cliente_data);
            $pedido->valor_total  = number_format($pedido->valorTotal(),2,',','.');
        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['pedidos'] = $pedidos;
        return $response;
    }



    //chamada para visualizar pedido de venda
    public function visualiza($pedido_venda_id)
    {
        $success = true;
        $log     = [];

        $pedido = PedidoVenda::where('id',$pedido_venda_id)->where(function($query){

            if($this->filial !== null)
            {
                $query->where('vxgloempfil_id',$this->filial->id);
                $query->orWhere('vxgloempfil_id',null);
            }

        })->first();


        if(!isset($pedido))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            $pedido->condicao_pagamento = CondicaoPagamento::where('erp_id',$pedido->vxglocpgto_erp_id)->first()->descricao;
            $pedido->tabela_preco = PrecoProduto::where('erp_id',$pedido->vxfattabprc_erp_id)->first()->descricao;
            $pedido->data_pedido  = Carbon::createFromFormat('Y-m-d H:i:s',$pedido->created_at)->format('d/m/Y');
            $pedido->hora_pedido  = Carbon::createFromFormat('Y-m-d H:i:s',$pedido->created_at)->format('H:i:s');
            $pedido->data_entrega = $pedido->data_entrega !== null ? Carbon::createFromFormat('Y-m-d',$pedido->data_entrega)->format('d/m/Y') : null;
            $pedido->cliente_data = json_decode($pedido->cliente_data);
            $pedido->valor_total  = number_format($pedido->valorTotal(),2,',','.');

            //busca os itens do pedido de venda
            $itens = PedidoItem::where('vxfatpvenda_id',$pedido_venda_id)->get();

            foreach($itens as $item)
            {
                $item->produto_data   = json_decode($item->produto_data);
                $item->preco_unitario = number_format($item->preco_unitario,2,',','.');
                $item->valor_desconto = number_format($item->valor_desconto,2,',','.');
                $item->valor_total    = number_format($item->valor_total,2,',','.');
            }

            $pedido->itens = $itens;
        }


        $response['success']   = $success;
        $response['log']       = $log;
        $response['pedido']    = isset($pedido) ? $pedido : null;
        return $response;
    }



    public function adicionaPost(Request $request)
    {
        $success = true;
        $log     = [];

        $rules = [
            'cliente_id'             => ['required'],
            'condicao_pagamento_id'  => ['required'],
            'tabela_preco_id'        => ['required'],
            'vendedor_id'            => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules, PedidoVenda::$messages);

        if ($validator->fails())
        {
            $success = false;

            foreach($validator->messages()->all() as $message)
            {
                $log[] = ['error' => $message];
            }
        }

        if($success)
        {
            //busca os dados para incluir o ERP ID
            $cliente  = Cliente::find($request['cliente_id']);
            $condicao = CondicaoPagamento::find($request['condicao_pagamento_id']);
            $vendedor = Vendedor::find($request['vendedor_id']);
            $preco    = PrecoProduto::find($request['tabela_preco_id']);


            $pedido = new PedidoVenda();
            $pedido->erp_id              = null;
            $pedido->situacao_pedido     = "A";
            $pedido->vxglocli_erp_id     = $cliente->erp_id;
            $pedido->vxglocpgto_erp_id   = $condicao->erp_id;
            $pedido->vxfatvend_erp_id    = $vendedor->erp_id;
            $pedido->vxfattabprc_erp_id  = $preco->erp_id;
            $pedido->data_entrega        = isset($request['data_entrega']) ? Carbon::createFromFormat('d/m/Y',$request['data_entrega'])->format('Y-m-d') : null;
            $pedido->observacao          = isset($request['observacao']) ? $request['observacao'] : '';
            $pedido->created_at          = new \DateTime();
            $pedido->updated_at          = new \DateTime();
            $pedido->save();

            if(isset($request['itens']))
            {
                $itens = [];

                foreach($request['itens'] as $item)
                {
                    $produto = Produto::find($item['produto_id']);

                    $pedidoItem = new PedidoItem();
                    $pedidoItem->vxfatpvenda_id   = $pedido->id;
                    $pedidoItem->vxgloprod_erp_id = $produto->erp_id;
                    $pedidoItem->quantidade       = $item['quantidade'];
                    $pedidoItem->preco_unitario   = number_format(Helper::formataDecimal($item['preco_unitario']),2,'.','');
                    $pedidoItem->valor_total      = number_format(Helper::formataDecimal($item['valor_total']),2,'.','');
                    $pedidoItem->created_at       = new \DateTime();
                    $pedidoItem->updated_at       = new \DateTime();
                    $pedidoItem->save();

                    $itens[] = $pedidoItem;
                }

                $pedido->itens = $itens;
            }

            //gera vex sync
            VexSyncController::adiciona('99,01','post',  $pedido->getTable(), $pedido->id,  $pedido->getWebservice('add')); // edit,get,delete: rest/ped_venda/$erp_id

            $log[]   = ['success' => 'Pedido cadastrado com sucesso'];

        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['pedido']  = isset($pedido) ? $pedido : null;
        return $response;
    }



    public function editaPost(Request $request, $pedido_id)
    {
        $success = true;
        $log     = [];

        $pedido = PedidoVenda::find($pedido_id);

        if(!isset($pedido))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            /**
             * Busca atualizações da nota fiscal no ERP, para verificar se já foi emitida nota fiscal para esse pedido
             *
             */
            $object = new \stdClass();
            $object->ws     = '/rest/vxfatpvenda/'.$pedido->erp_id;
            $object->tabela = 'vx_fat_pvenda';

            ErpVexSync::update($object);

            $nfEmitida = PedidoItem::where('vxfatpvenda_id',$pedido_id)->where('nota_fiscal','!=',null)->first();

            if(isset($nfEmitida))
            {
                $success = false;
                $log[]   = ['error' => 'Já foi emitida alguma nota fiscal para esse pedido'];
            }

            if($success)
            {
                $rules = [
                    'cliente_id'             => ['required'],
                    'condicao_pagamento_id'  => ['required'],
                    'tabela_preco_id'        => ['required'],
                    'vendedor_id'            => ['required'],
                ];

                $validator = Validator::make($request->all(), $rules, PedidoVenda::$messages);

                if ($validator->fails())
                {
                    $success = false;

                    foreach($validator->messages()->all() as $message)
                    {
                        $log[] = ['error' => $message];
                    }
                }

                if($success)
                {
                    //busca os dados para incluir o ERP ID
                    $cliente  = Cliente::find($request['cliente_id']);
                    $condicao = CondicaoPagamento::find($request['condicao_pagamento_id']);
                    $vendedor = Vendedor::find($request['vendedor_id']);
                    $preco    = PrecoProduto::find($request['tabela_preco_id']);


                    $pedido->situacao_pedido     = "A";
                    $pedido->vxglocli_erp_id     = $cliente->erp_id;
                    $pedido->vxglocpgto_erp_id   = $condicao->erp_id;
                    $pedido->vxfatvend_erp_id    = $vendedor->erp_id;
                    $pedido->vxfattabprc_erp_id  = $preco->erp_id;
                    $pedido->data_entrega        = isset($request['data_entrega']) ? Carbon::createFromFormat('d/m/Y',$request['data_entrega'])->format('Y-m-d') : null;
                    $pedido->observacao          = isset($request['observacao']) ? $request['observacao'] : '';
                    $pedido->created_at          = new \DateTime();
                    $pedido->updated_at          = new \DateTime();
                    $pedido->save();

                    /**
                     * TODO: verificar se serão apagados todos os itens e cadastrados novamente
                     *
                     */
                    if(isset($request['itens']))
                    {
                        $itens = [];

                        //exclui todos os itens
                        PedidoItem::where('vxfatpvenda_id',$pedido_id)->delete();

                        foreach($request['itens'] as $item)
                        {
                            $produto = Produto::find($item['produto_id']);

                            $pedidoItem = new PedidoItem();
                            $pedidoItem->vxfatpvenda_id   = $pedido->id;
                            $pedidoItem->vxgloprod_erp_id = $produto->erp_id;
                            $pedidoItem->quantidade       = $item['quantidade'];
                            $pedidoItem->preco_unitario   = number_format(Helper::formataDecimal($item['preco_unitario']),2,'.','');
                            $pedidoItem->valor_total      = number_format(Helper::formataDecimal($item['valor_total']),2,'.','');
                            $pedidoItem->created_at       = new \DateTime();
                            $pedidoItem->updated_at       = new \DateTime();
                            $pedidoItem->save();

                            $itens[] = $pedidoItem;
                        }

                        $pedido->itens = $itens;
                    }

                    //gera vex sync
                    VexSyncController::adiciona('99,01', 'post',  $pedido->getTable(), $pedido->id,  $pedido->getWebservice('add')); // edit,get,delete: rest/ped_venda/$erp_id

                    $log[]   = ['success' => 'Pedido cadastrado com sucesso'];

                }
            }
        }



        $response['success'] = $success;
        $response['log']     = $log;
        $response['pedido']  = isset($pedido) ? $pedido : null;
        return $response;
    }



    //post via vexsync
    public static function syncPost($sync)
    {
        $success = true;
        $log     = '';

        //busca dados da assinatura
        $assinatura = Assinatura::first();

        //busca item da tabela que será adiciona no ERP
        $object = DB::table($sync->tabela)->where('id', $sync->tabela_id)->first();


        //busca os pedidos itens, do registro em questão
        $itens = [];

        $pedidosItens = PedidoItem::where('vxfatpvenda_id',$object->id)->get();

        foreach($pedidosItens as $item)
        {
            $pedidoItem = new \stdClass();
            $pedidoItem->vxgloprod_erp_id = $item->vxgloprod_erp_id;
            $pedidoItem->quantidade       = (string) $item->quantidade;
            $pedidoItem->preco_unitario   = $item->preco_unitario;
            $pedidoItem->preco_venda      = $item->preco_venda;
            $pedidoItem->valor_desconto   = $item->valor_desconto;
            $pedidoItem->valor_total      = $item->valor_total;

            $itens[] = $pedidoItem;
        }

        $object->itens = $itens;

        //formata objeto para enviar no vexsync
        $object = Helper::formataSyncObject($object);


        //tratamento de log
        $registro  = "Iniciando VEX Sync do registro (ID) ".$sync->id."\n\n";
        $registro .= "Dados a serem enviados ".json_encode($object)."\n\n";



        //insere item no ERP
        try 
        {
            $resultSuccess = null;

            while($resultSuccess == null)
            {
                $guzzle  = new Client();
                $result  = $guzzle->request('POST', $assinatura->webservice_base . $sync->webservice, [
                    'headers'     => [
                        'Content-Type'    => 'application/json',
                        'tenantId'        => $sync->tenant
                    ], 
                    'body' => json_encode($object)
                ]);
                
                $result = json_decode($result->getBody());

                if(isset($result->success))
                {
                    $resultSuccess = $result->success;
                }
            }
            
    
            if($resultSuccess == false)
            {
                $success = false;
                $log     = isset($result->log) ? $result->log : $result->message;


                $registro .= "ERRO: $log";
            }
            else 
            {
                //atualiza o registro com o erp_id
                $object = Helper::retornoERP($result->result);
                $object = json_decode($object);

                DB::table($sync->tabela)->where('id', $sync->tabela_id)->update([
                    'erp_id' => $object->erp_id,
                ]);

                DB::table('vx_fat_ipvend')->where('vxfatpvenda_id', $sync->tabela_id)->update([
                    'vxfatpvenda_erp_id' => $object->erp_id,
                ]);

                $log = 'Sincronização realizada com sucesso';


                $registro .= "VEX Sync atualizado com sucesso na Central VEX: $log";

            }

        }
        catch(\Exception $e)
        {
            $success = false;
            $log     = $e->getMessage();


            $registro .= "\nERRO: Linha: {$e->getLine()}\nArquivo: {$e->getFile()}\nCódigo: {$e->getCode()}\nMensagem {$e->getMessage()}";

        }

        Helper::logFile('vex-sync-central.log', $registro);


        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //put via vexsync
    public static function syncPut($sync)
    {
        $success = true;
        $log     = '';

        //busca dados da assinatura
        $assinatura = Assinatura::first();

        //busca item da tabela que será adiciona no ERP
        $object = DB::table($sync->tabela)->where('id', $sync->tabela_id)->first();


        //busca os pedidos itens, do registro em questão
        $itens = [];

        $pedidosItens = PedidoItem::where('vxfatpvenda_id',$object->id)->get();

        foreach($pedidosItens as $item)
        {
            $pedidoItem = new \stdClass();
            $pedidoItem->vxgloprod_erp_id = $item->vxgloprod_erp_id;
            $pedidoItem->quantidade       = (string) $item->quantidade;
            $pedidoItem->preco_unitario   = $item->preco_unitario;
            $pedidoItem->preco_venda      = $item->preco_venda;
            $pedidoItem->valor_desconto   = $item->valor_desconto;
            $pedidoItem->valor_total      = $item->valor_total;

            $itens[] = $pedidoItem;
        }

        $object->itens = $itens;

        //formata objeto para enviar no vexsync
        $object = Helper::formataSyncObject($object);


        //tratamento de log
        $registro  = "Iniciando VEX Sync do registro (ID) ".$sync->id."\n\n";
        $registro .= "Dados a serem enviados ".json_encode($object)."\n\n";



        //insere item no ERP
        try
        {
            $resultSuccess = null;

            while($resultSuccess == null)
            {
                $guzzle  = new Client();
                $result  = $guzzle->request('POST', $assinatura->webservice_base . $sync->webservice, [
                    'headers'     => [
                        'Content-Type'    => 'application/json',
                        'tenantId'        => $sync->tenant
                    ],
                    'body' => json_encode($object)
                ]);

                $result = json_decode($result->getBody());

                if(isset($result->success))
                {
                    $resultSuccess = $result->success;
                }
            }


            if($resultSuccess == false)
            {
                $success = false;
                $log     = isset($result->log) ? $result->log : $result->message;


                $registro .= "ERRO: $log";
            }
            else
            {
                //atualiza o registro com o erp_id
                $object = Helper::retornoERP($result->result);
                $object = json_decode($object);

                DB::table($sync->tabela)->where('id', $sync->tabela_id)->update([
                    'erp_id' => $object->erp_id,
                ]);

                DB::table('vx_fat_ipvend')->where('vxfatpvenda_id', $sync->tabela_id)->update([
                    'vxfatpvenda_erp_id' => $object->erp_id,
                ]);

                $log = 'Sincronização realizada com sucesso';


                $registro .= "VEX Sync atualizado com sucesso na Central VEX: $log";

            }

        }
        catch(\Exception $e)
        {
            $success = false;
            $log     = $e->getMessage();


            $registro .= "\nERRO: Linha: {$e->getLine()}\nArquivo: {$e->getFile()}\nCódigo: {$e->getCode()}\nMensagem {$e->getMessage()}";

        }

        Helper::logFile('vex-sync-central.log', $registro);


        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }



}
