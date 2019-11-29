<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Assinatura;
use App\Cliente;
use App\CondicaoPagamento;
use App\EmpresaFilial;
use App\Http\Controllers\Mobile\VexSyncController;
use App\Lote;
use App\PedidoVenda;
use App\PedidoItem;
use App\Produto;
use App\TabelaPreco;
use App\TabelaPrecoProduto;
use App\Vendedor;
use App\Http\Controllers\Erp\VexSyncController as ErpVexSync;

//mails

//framework
use App\Http\Controllers\Controller;
use App\VexSync;
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
    protected $vendedor;
    private static $logMessage = "Execução de VEX Sync em Mobile\PedidoVendaController\n\n";

    //construct
    public function __construct($filialId = null, $user = null)
    {
         $this->filial   = isset($filialId)           ? EmpresaFilial::where('filial_erp_id',$filialId)->first() : null;
         $this->vendedor = isset($user->vxfatvend_id) ? Vendedor::find($user->vxfatvend_id) : null;
    }


    public function lista(Request $request)
    {
        $success = true;
        $log     = [];


        $pedidos = PedidoVenda::join('vx_glo_cli','vx_glo_cli.erp_id','=','vx_fat_pvenda.vxglocli_erp_id')
            ->select('vx_fat_pvenda.*')
            ->where('vxfatvend_erp_id',$this->vendedor->erp_id)
            ->where(function($query){
                if($this->filial !== null)
                {
                    $query->where('vx_fat_pvenda.vxgloempfil_id',$this->filial->id);
                    $query->orWhere('vx_fat_pvenda.vxgloempfil_id',null);
                }
            })->where(function ($query) use ($request){

                if(isset($request['termo']))
                {
                    $query->orWhereRaw('vx_fat_pvenda.erp_id like "%'.$request['termo'].'%"');
                    $query->orWhereRaw('vx_glo_cli.razao_social like "%'.$request['termo'].'%"');
                    $query->orWhereRaw('vx_glo_cli.nome_fantasia like "%'.$request['termo'].'%"');
                }

            })->where(function($query){

                //listamos os pedidos dos últimos 15 dias e os pedidos em aberto
                $query->orWhere('vx_fat_pvenda.created_at','>=',Carbon::now()->subDays(15)->format('Y-m-d 00:00:00'));
                $query->orWhere('situacao_pedido','=','A');

            })->orderBy('vx_fat_pvenda.created_at','desc')->get();



        foreach($pedidos as $pedido)
        {
            $pedido->condicao_pagamento = CondicaoPagamento::where('erp_id',$pedido->vxglocpgto_erp_id)->first()->descricao;
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

        })->where('vxfatvend_erp_id',$this->vendedor->erp_id)->first();


        if(!isset($pedido))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            $pedido->condicao_pagamento = CondicaoPagamento::where('erp_id',$pedido->vxglocpgto_erp_id)->first()->descricao;
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
            //--------------------------------------------------------------------------------------------------------//
            // - Formata os itens enviados, escolhendo automaticamente os lotes a serem utilizados na venda,
            // de acordo com saldo e data de validade
            //--------------------------------------------------------------------------------------------------------//
            if(!isset($request['itens']))
            {
                $success = false;
                $log[]   = ['error' => 'Itens não informados'];
            }
            else
            {
                if(count($request['itens']) == 0)
                {
                    $success = false;
                    $log[]   = ['error' => 'Nenhum item enviado'];
                }
                else
                {
                    $itens = [];

                    $lotesUtilizados = [];

                    for($i = 0; $i < count($request['itens']); $i++)
                    {
                        $produtoId        = $request['itens'][$i]['id'];
                        $tabelaId         = $request['itens'][$i]['tabela_id'];
                        $produtoDescricao = $request['itens'][$i]['descricao'];
                        $precoOriginal    = $request['itens'][$i]['preco_original'];
                        $precoAplicado    = $request['itens'][$i]['preco_aplicado'];
                        $quantidade       = $request['itens'][$i]['quantidade'];
                        $pendente         = $quantidade;

                        while($pendente > 0)
                        {
                            $lote = Lote::select('vx_est_lote.*',DB::raw('(saldo - empenho) as saldo_real'))
                                ->where('vxgloprod_id',$produtoId)
                                ->where('dt_valid','>',Carbon::now()->format('Y-m-d'))
                                ->where(DB::raw('(saldo - empenho)'),'>','0')
                                ->whereNotIn('id',$lotesUtilizados)
                                ->orderBy('dt_valid','asc')
                                ->first();

                            if(isset($lote))
                            {
                                $success = false;
                                $log[]   = ['error' => 'Não há saldo suficiente para o produto '.$produtoDescricao];
                            }
                            else
                            {
                                $itens[] = [
                                    'produto_id' => $produtoId,
                                    'lote_id' => $lote->id,
                                    'tabela_id' => $tabelaId,
                                    'preco_original' => $precoOriginal,
                                    'preco_aplicado' => $precoAplicado,
                                    'quantidade' => $quantidade,
                                ];

                                $pendente = $pendente - $lote->saldo_real;

                                $lotesUtilizados[] = $lote->id;
                            }
                        }
                    }
                }
            }



            //caso não tenha ocorrido problemas, cadastramos o pedido e os itens do pedido
            if($success)
            {

                //busca os dados para incluir o ERP ID
                $cliente  = Cliente::find($request['cliente_id']);
                $condicao = CondicaoPagamento::find($request['condicao_pagamento_id']);

                $pedido = new PedidoVenda();
                $pedido->situacao_pedido     = "A";
                $pedido->vxgloempfil_id      = $this->filial->id;
                $pedido->vxglocli_erp_id     = $cliente->erp_id;
                $pedido->vxglocpgto_erp_id   = $condicao->erp_id;
                $pedido->vxfatvend_erp_id    = $this->vendedor->erp_id;
                $pedido->cliente_data        = json_encode($cliente, JSON_UNESCAPED_UNICODE);
                $pedido->data_entrega        = isset($request['data_entrega']) ? Carbon::parse($request['data_entrega'])->format('Y-m-d') : null;
                $pedido->status_entrega      = isset($request['status_entrega']) ? $request['status_entrega'] : '1';
                $pedido->observacao          = Helper::formataString($request['observacao_nota'] !== null ? $request['observacao_nota'] : '');
                $pedido->obs_interna         = Helper::formataString($request['observacao_interna'] !== null ? $request['observacao_interna'] : 'Nenhuma observacao inserida pelo usuario');
                $pedido->created_at          = new \DateTime();
                $pedido->updated_at          = new \DateTime();
                $pedido->save();


                for($i = 0; $i < count($itens); $i++)
                {

                    //formata as variáveis enviadas
                    $tabelaId      = $itens[$i]['tabela_id'];
                    $produtoId     = $itens[$i]['produto_id'];
                    $loteId        = $itens[$i]['lote_id'];
                    $precoOriginal = (float) Helper::formataDecimal($itens[$i]['preco_original']);
                    $precoAplicado = (float) Helper::formataDecimal($itens[$i]['preco_aplicado']);
                    $quantidade    = (int) $itens[$i]['quantidade'];

                    //busca os objetos relacionados e executa os cálculos dos valores
                    $tabela  = TabelaPreco::find($tabelaId);
                    $produto = Produto::find($produtoId);
                    $lote    = Lote::find($loteId);

                    if($precoOriginal == $precoAplicado)
                    {
                        $desconto  = 0.00;
                        $acrescimo = 0.00;
                    }
                    else if($precoAplicado < $precoOriginal)
                    {
                        $desconto  = abs( ($quantidade * $precoOriginal) - ($quantidade * $precoAplicado) );
                        $acrescimo = 0.00;
                    }
                    else if($precoAplicado > $precoOriginal)
                    {
                        $desconto  = 0.00;
                        $acrescimo = abs( ($quantidade * $precoAplicado) - ($quantidade * $precoOriginal) );
                    }

                    $pedidoItem = new PedidoItem();
                    $pedidoItem->vxfatpvenda_id     = $pedido->id;
                    $pedidoItem->vxgloprod_erp_id   = $produto->erp_id;
                    $pedidoItem->vxfattabprc_erp_id = $tabela->erp_id;
                    $pedidoItem->vxestarmz_erp_id   = $lote->armazem->erp_id;
                    $pedidoItem->vxestlote_erp_id   = $lote->erp_id;
                    $pedidoItem->quantidade         = $quantidade;
                    $pedidoItem->produto_data       = json_encode($produto, JSON_UNESCAPED_UNICODE);
                    $pedidoItem->preco_unitario     = $precoOriginal;
                    $pedidoItem->preco_venda        = $precoAplicado;
                    $pedidoItem->valor_desconto     = $desconto;
                    $pedidoItem->valor_acrescimo    = $acrescimo;
                    $pedidoItem->valor_total        = $quantidade * $precoAplicado;
                    $pedidoItem->created_at         = new \DateTime();
                    $pedidoItem->updated_at         = new \DateTime();
                    $pedidoItem->save();


                    //registra o empenho da quantidade do lote
                    LoteController::atualizaQuantidadeEmpenhada($lote->erp_id);
                }


                //gera vex sync
                VexSyncController::adiciona('01,01','post',  $pedido->getTable(), $pedido->id,  $pedido->getWebservice('add')); // edit,get,delete: rest/ped_venda/$erp_id

                $log[]   = ['success' => 'Pedido cadastrado com sucesso'];

            }

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
                    $vendedor = $this->vendedor;
                    $preco    = TabelaPreco::find($request['tabela_preco_id']);


                    $pedido->vxgloempfil_id      = $this->filial->id;
                    $pedido->vxglocli_erp_id     = $cliente->erp_id;
                    $pedido->vxglocpgto_erp_id   = $condicao->erp_id;
                    $pedido->vxfatvend_erp_id    = $vendedor->erp_id;
                    $pedido->vxfattabprc_erp_id  = $preco->erp_id;
                    $pedido->situacao_pedido     = "A";
                    $pedido->cliente_data        = json_encode($cliente,JSON_UNESCAPED_UNICODE);
                    $pedido->data_entrega        = isset($request['data_entrega']) ? Carbon::createFromFormat('Y-m-d',$request['data_entrega'])->format('Y-m-d') : null;
                    $pedido->observacao          = isset($request['observacao']) ? $request['observacao'] : '';
                    $pedido->updated_at          = new \DateTime();
                    $pedido->save();


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
                            $pedidoItem->produto_data     = json_encode($produto, JSON_UNESCAPED_UNICODE);
                            $pedidoItem->quantidade       = $item['quantidade'];
                            $pedidoItem->preco_unitario   = number_format(Helper::formataDecimal($item['preco_unitario']),2,'.','');
                            $pedidoItem->preco_venda      = number_format(Helper::formataDecimal($item['preco_venda']),2,'.','');
                            $pedidoItem->valor_desconto   = number_format(Helper::formataDecimal($item['valor_desconto']),2,'.','');
                            $pedidoItem->valor_total      = number_format(Helper::formataDecimal($item['valor_total']),2,'.','');
                            $pedidoItem->created_at       = new \DateTime();
                            $pedidoItem->updated_at       = new \DateTime();
                            $pedidoItem->save();

                            $itens[] = $pedidoItem;
                        }

                        $pedido->itens = $itens;
                    }

                    //gera vex sync
                    if(isset($pedido->erp_id))
                    {
                        VexSyncController::adiciona(Helper::formataTenantId($pedido->vxgloempfil_id), 'put',  $pedido->getTable(), $pedido->id,  $pedido->getWebservice('edit/'.$pedido->erp_id));
                    }

                    $log[]   = ['success' => 'Pedido atualizado com sucesso'];

                }
            }
        }



        $response['success'] = $success;
        $response['log']     = $log;
        $response['pedido']  = isset($pedido) ? $pedido : null;
        return $response;
    }



    public function excluiPost(Request $request, $pedido_id)
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
            if(isset($pedido->erp_id))
            {
                $object = new \stdClass();
                $object->ws     = '/rest/vxfatpvenda/'.$pedido->erp_id;
                $object->tabela = 'vx_fat_pvenda';

                ErpVexSync::update($object);
            }


            $nfEmitida = PedidoItem::where('vxfatpvenda_id',$pedido_id)->where('nota_fiscal','!=',null)->where('nota_fiscal','!=',"")->first();

            if(isset($nfEmitida))
            {
                $success = false;
                $log[]   = ['error' => 'Já foi emitida alguma nota fiscal para esse pedido'];
            }

            if($success)
            {
                try
                {
                    $assinatura = Assinatura::first();

                    $guzzle  = new Client();
                    $result  = $guzzle->request('DELETE', $assinatura->webservice_base . $pedido->getWebservice() . $pedido->erp_id, [
                        'headers'     => [
                            'Content-Type'  => 'application/json',
                            'tenantId'      => Helper::formataTenantId($pedido->vxgloempfil_id)
                        ],
                        'body' => json_encode([
                            'erp_id'          => $pedido->erp_id,
                            'vxglocli_erp_id' => $pedido->vxglocli_erp_id,
                            'vxglocli_loja'   => json_decode($pedido->cliente_data)->loja,
                        ])
                    ]);
                    $result  = json_decode($result->getBody());

                    if($result->success !== true)
                    {
                        $success  = false;
                        $log[]    = ['error' => 'Não foi possível excluir o pedido. Por favor acione a equipe de suporte'];
                    }
                }
                catch(\Exception $e)
                {
                    $success  = false;
                    $log[]    = ['error' => 'Não foi possível excluir o pedido. Por favor acione a equipe de suporte'];
                }



                if($success)
                {
                    //exclui o pedido no banco de dados
                    $pedido->delete();

                    //exclui os itens de pedido que não foram enviados na requisição
                    PedidoItem::where('vxfatpvenda_id',$pedido_id)->delete();


                    $log[]   = ['success' => 'Pedido excluído com sucesso'];
                }
            }
        }



        $response['success'] = $success;
        $response['log']     = $log;
        $response['pedido']  = isset($pedido) ? $pedido : null;
        return $response;
    }



    //chamada para visualizar item do pedido de venda
    public function visualizaItem($pedido_venda_id, $item_id)
    {
        $success = true;
        $log     = [];

        $pedido = PedidoVenda::where('id',$pedido_venda_id)->where(function($query){

            if($this->filial !== null)
            {
                $query->where('vxgloempfil_id',$this->filial->id);
                $query->orWhere('vxgloempfil_id',null);
            }

        })->where('vxfatvend_erp_id',$this->vendedor->erp_id)->first();


        if(!isset($pedido))
        {
            $success = false;
            $log[]   = ['error' => 'Pedido não encontrado'];
        }
        else
        {
            $item = PedidoItem::where('id',$item_id)->where('vxfatpvenda_id',$pedido_venda_id)->first();

            if(!isset($item))
            {
                $success = false;
                $log[]   = ['error' => 'Item não encontrado'];
            }
            else
            {
                $item->produto_data = json_decode($item->produto_data, true);
            }
        }


        $response['success']   = $success;
        $response['log']       = $log;
        $response['pedido']    = isset($pedido) ? $pedido : null;
        $response['item']      = isset($item)   ? $item   : null;
        return $response;
    }



    //post via vexsync
    public static function syncPost($sync)
    {
        $success = true;
        $log     = self::$logMessage;

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
            $pedidoItem->vex_id             = $item->id;
            $pedidoItem->vxgloprod_erp_id   = $item->vxgloprod_erp_id;
            $pedidoItem->vxfattabprc_erp_id = $item->vxfattabprc_erp_id;
            $pedidoItem->vxestarmz_erp_id   = $item->vxestarmz_erp_id;
            $pedidoItem->vxestlote_erp_id   = $item->vxestlote_erp_id;
            $pedidoItem->quantidade         = (string) $item->quantidade;
            $pedidoItem->alerta_estoque     = $item->alerta_estoque;
            $pedidoItem->preco_unitario     = $item->preco_unitario;
            $pedidoItem->preco_venda        = $item->preco_venda;
            $pedidoItem->valor_desconto     = $item->valor_desconto;
            $pedidoItem->valor_total        = $item->valor_total;

            $itens[] = $pedidoItem;
        }

        $object->vex_id = $object->id;
        $object->itens  = $itens;

        unset($object->cliente_data);

        //formata objeto para enviar no vexsync
        $object = Helper::formataSyncObject($object);

        //adiciona ao log, objeto a ser enviado
        $log .= json_encode($object)."\n\n";

        //insere item no ERP
        try 
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

    
            if($result->success == false)
            {
                $success = false;
                $message = isset($result->log) ? $result->log : $result->message;

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

                //caso tenha retornado array de itens, significa que um ou mais itens possuem alerta de estoque
                if(isset($object->itens))
                {
                    DB::table($sync->tabela)->where('id', $sync->tabela_id)->update([
                        'situacao_pedido' => 'S',
                    ]);

                    foreach($object->itens as $item)
                    {
                        $item = json_decode(Helper::retornoERP($item));

                        $pedidoItem = PedidoItem::find($item->vex_id);

                        if(isset($pedidoItem))
                        {
                            $pedidoItem->alerta_estoque = $item->alerta_estoque;
                            $pedidoItem->updated_at     = new \DateTime();
                            $pedidoItem->save();
                        }
                    }
                }

                $message = 'Sincronização realizada com sucesso';

            }

            $log .= $message;


        }
        catch(\Exception $e)
        {
            $success = false;
            $log    .= "Ocorreu um erro ao realizar o procedimento.\n\n";
            $log    .= 'Code '.$e->getFile().' - File: '.$e->getFile().' ('.$e->getLine().') - Message: '.$e->getMessage()."\n\n";
        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //put via vexsync
    public static function syncPut($sync)
    {
        $success = true;
        $log     = self::$logMessage;

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
            $pedidoItem->vex_id             = $item->id;
            $pedidoItem->vxgloprod_erp_id   = $item->vxgloprod_erp_id;
            $pedidoItem->vxfattabprc_erp_id = $item->vxfattabprc_erp_id;
            $pedidoItem->vxestarmz_erp_id   = $item->vxestarmz_erp_id;
            $pedidoItem->vxestlote_erp_id   = $item->vxestlote_erp_id;
            $pedidoItem->alerta_estoque     = $item->alerta_estoque;
            $pedidoItem->quantidade         = (string) $item->quantidade;
            $pedidoItem->preco_unitario     = $item->preco_unitario;
            $pedidoItem->preco_venda        = $item->preco_venda;
            $pedidoItem->valor_desconto     = $item->valor_desconto;
            $pedidoItem->valor_total        = $item->valor_total;

            $itens[] = $pedidoItem;
        }

        $object->vex_id = $object->id;
        $object->itens  = $itens;

        unset($object->cliente_data);

        //formata objeto para enviar no vexsync
        $object = Helper::formataSyncObject($object);

        //adiciona ao log, objeto a ser enviado
        $log .= json_encode($object)."\n\n";

        //insere item no ERP
        try
        {
            $guzzle  = new Client();
            $result  = $guzzle->request('PUT', $assinatura->webservice_base . $sync->webservice, [
                'headers'     => [
                    'Content-Type'    => 'application/json',
                    'tenantId'        => $sync->tenant
                ],
                'body' => json_encode($object)
            ]);

            $result = json_decode($result->getBody());


            if($result->success == false)
            {
                $success = false;
                $message = isset($result->log) ? $result->log : $result->message;
            }
            else
            {
                //caso tenha retornado array de itens, significa que um ou mais itens possuem alerta de estoque
                if(isset($object->itens))
                {
                    DB::table($sync->tabela)->where('id', $sync->tabela_id)->update([
                        'situacao_pedido' => 'S',
                    ]);

                    foreach($object->itens as $item)
                    {
                        $item = json_decode(Helper::retornoERP($item));

                        $pedidoItem = PedidoItem::find($item->vex_id);

                        if(isset($pedidoItem))
                        {
                            $pedidoItem->alerta_estoque = $item->alerta_estoque;
                            $pedidoItem->updated_at     = new \DateTime();
                            $pedidoItem->save();
                        }
                    }
                }

                $message = 'Sincronização realizada com sucesso';
            }

            $log .= $message;

        }
        catch(\Exception $e)
        {
            $success = false;
            $log     .= "Ocorreu um erro ao realizar o procedimento.\n\n";
            $log     .= 'Code '.$e->getFile().' - File: '.$e->getFile().' ('.$e->getLine().') - Message: '.$e->getMessage()."\n\n";
        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }



}
