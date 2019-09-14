<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
use App\Assinatura;
use App\Cliente;
use App\CondicaoPagamento;
use App\Files\PedidoVenda\Main;
use App\Http\Controllers\Mobile\VexSyncController;
use App\Lote;
use App\PedidoItem;
use App\PedidoVenda;
use App\Configuracao;

//mails

//framework
use App\Http\Controllers\Controller;
use App\Produto;
use App\Risco;
use App\TabelaPreco;
use App\TabelaPrecoProduto;
use App\Utils\Helper;
use App\Vendedor;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

//packages

//extras
use Validator;


class PedidoVendaController extends Controller
{

    protected $empfilId;
    protected $vendedorId;
    protected $vendedorErpId;

    //construct
    public function __construct()
    {
        $this->empfilId      = Auth::user()->userEmpresaFilial->empfil->id;
        $this->vendedorId    = isset(Auth::user()->vendedor) ? Auth::user()->vendedor->id : '1';
        $this->vendedorErpId = isset(Auth::user()->vendedor) ? Auth::user()->vendedor->erp_id : null;
    }

    //retorna array do objeto
    public function lista()
    {
        $success = true;
        $log     = [];

        $pedidos = PedidoVenda::join('vx_glo_cli','vx_glo_cli.erp_id','=','vx_fat_pvenda.vxglocli_erp_id')
            ->select('vx_fat_pvenda.*','vx_glo_cli.vxfatvend_erp_id_1','vx_glo_cli.vxfatvend_erp_id_2')
            ->where(function($query){

                $query->where('vx_fat_pvenda.vxgloempfil_id',$this->empfilId);
                $query->orWhere('vx_fat_pvenda.vxgloempfil_id','=',null);

            })->where(function($query){

                if((int)Auth::user()->vxwebperfil_id !== 1)
                {
                    $query->where('vx_fat_pvenda.vxfatvend_erp_id',$this->vendedorErpId);
                    $query->orWhere('vx_glo_cli.vxfatvend_erp_id_1',Auth::user()->vendedor->erp_id);
                    $query->orWhere('vx_glo_cli.vxfatvend_erp_id_2',Auth::user()->vendedor->erp_id);
                }

            })->orderBy('vx_fat_pvenda.updated_at','desc')->get();

        $response['success'] = $success;
        $response['log']     = $log;
        $response['pedidos'] = $pedidos;
        return $response;
    }


    //chamada de tela para gerar um pedido
    public function adiciona()
    {
        $success = true;
        $log     = [];

        $pedido = new PedidoVenda();

        // Busca os clientes cadastrados
        $clientes = Cliente::where(function($query){

            $query->where('vxgloempfil_id',$this->empfilId);
            $query->orWhere('vxgloempfil_id','=',null);

        })->where(function($query){

            if((int)Auth::user()->vxwebperfil_id !== 1)
            {
                $query->where('vxfatvend_erp_id_1',Auth::user()->vendedor->erp_id);
                $query->orWhere('vxfatvend_erp_id_2',Auth::user()->vendedor->erp_id);
            }

        })->where('status','1')->orderBy('razao_social','asc')->get();


        if(count($clientes) == 0)
        {
            $success = false;
            $log[]   = ['error' => 'Não é possível gerar um novo pedido, pois não há clientes ativos cadastrados'];
        }

        // Busca os produtos cadastrados
        $produtos = Produto::where(function($query){

            $query->where('vxgloempfil_id',$this->empfilId);
            $query->orWhere('vxgloempfil_id','=',null);

        })->where('status','1')->orderBy('descricao','asc')->get();

        if(count($produtos) == 0)
        {
            $success = false;
            $log[]   = ['error' => 'Não é possível gerar um novo pedido, pois não há produtos cadastrados'];
        }
        else
        {
            $itens = [];

            //verifica se os produtos possuem saldo em estoque
            foreach($produtos as $produto)
            {
                $lote = Lote::where('vxgloprod_id',$produto->id)
                    ->where('saldo','>','0')
                    ->where('dt_valid','!=',null)
                    ->where('dt_valid','>=',Carbon::now()->addDays(1)->format('Y-m-d'))
                    ->first();

                if(isset($lote))
                {
                    $lote->saldo = $lote->saldo - $lote->empenho;

                    if($lote->saldo > 0)
                    {
                        $itens[] = $produto;
                    }
                }
            }

            if(count($itens) == 0)
            {
                $success = false;
                $log[]   = ['error' => 'Não é possível gerar um novo pedido, pois não há produtos com saldo em estoque'];
            }
            else
            {
                $produtos = $itens;
            }
        }


        // Busca as tabelas de preços cadastradas
        $tabelas = TabelaPreco::where(function($query){

            $query->where('vxgloempfil_id',$this->empfilId);
            $query->orWhere('vxgloempfil_id','=',null);

        })->orderBy('descricao','asc')->get();

        if(count($tabelas) == 0)
        {
            $success = false;
            $log[]   = ['error' => 'Não é possível gerar um novo pedido, pois não há tabelas de preços cadastradas'];
        }

        // Busca as condições de pagamento cadastradas
        $condicoes = CondicaoPagamento::where('vxgloempfil_id',$this->empfilId)->where('web','1')->where('status','1')->orderBy('descricao','asc')->get();

        if(count($condicoes) == 0)
        {
            $success = false;
            $log[]   = ['error' => 'Não é possível gerar um novo pedido, pois não é condições de pagamento ativas cadastradas'];
        }


        //busca classificação de risco para verificar máximo de desconto permitido
        $riscos = Risco::all();

        $response['success']   = $success;
        $response['log']       = $log;
        $response['pedido']    = $pedido;
        $response['clientes']  = $clientes;
        $response['produtos']  = $produtos;
        $response['tabelas']   = $tabelas;
        $response['condicoes'] = $condicoes;
        $response['riscos']    = $riscos;
        return $response;
    }


    //post para adicionar pedido
    public function adicionaPost(Request $request)
    {
        $success = true;
        $log     = [];

        $rules = [];

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
            $cliente  = Cliente::find($request['vxglocli_id']);
            $condicao = CondicaoPagamento::find($request['vxglocpgto_id']);
            $vendedor = Vendedor::find($this->vendedorId);

            $pedido = new PedidoVenda();
            $pedido->situacao_pedido     = "A";
            $pedido->vxgloempfil_id      = $this->empfilId;
            $pedido->vxglocli_erp_id     = $cliente->erp_id;
            $pedido->vxglocpgto_erp_id   = $condicao->erp_id;
            $pedido->vxfatvend_erp_id    = $vendedor->erp_id;
            $pedido->cliente_data        = json_encode($cliente, JSON_UNESCAPED_UNICODE);
            $pedido->data_entrega        = isset($request['data_entrega']) ? Carbon::createFromFormat('d/m/Y',$request['data_entrega'])->format('Y-m-d') : null;
            $pedido->status_entrega      = $request['status_entrega'];
            $pedido->observacao          = isset($request['observacao']) ? $request['observacao'] : '';
            $pedido->obs_interna         = isset($request['obs_interna']) ? $request['obs_interna'] : '';
            $pedido->created_at          = new \DateTime();
            $pedido->updated_at          = new \DateTime();
            $pedido->save();

            if(isset($request['produto_id']))
            {
                for($i = 0; $i < count($request['produto_id']); $i++)
                {
                    $tabela  = TabelaPreco::find($request['produto_tabela_id'][$i]);

                    $produto = Produto::find($request['produto_id'][$i]);

                    $lote    = Lote::find($request['produto_lote_id'][$i]);

                    if((float) Helper::formataDecimal($request['produto_valor_desconto'][$i]) == 0.00)
                    {
                        $desconto  = 0.00;
                        $acrescimo = 0.00;
                    }
                    elseif((float) Helper::formataDecimal($request['produto_valor_desconto'][$i]) > 0.00)
                    {
                        $desconto  = (float) Helper::formataDecimal($request['produto_valor_desconto'][$i]);
                        $acrescimo = 0.00;
                    }
                    elseif((float) Helper::formataDecimal($request['produto_valor_desconto'][$i]) < 0.00)
                    {
                        $desconto  = 0.00;
                        $acrescimo = abs((float) Helper::formataDecimal($request['produto_valor_desconto'][$i]));
                    }

                    $pedidoItem = new PedidoItem();
                    $pedidoItem->vxfatpvenda_id     = $pedido->id;
                    $pedidoItem->vxgloprod_erp_id   = $produto->erp_id;
                    $pedidoItem->vxfattabprc_erp_id = $tabela->erp_id;
                    $pedidoItem->vxestarmz_erp_id   = $lote->armazem->erp_id;
                    $pedidoItem->vxestlote_erp_id   = $lote->erp_id;
                    $pedidoItem->quantidade         = $request['produto_quantidade'][$i];
                    $pedidoItem->produto_data       = json_encode($produto, JSON_UNESCAPED_UNICODE);
                    $pedidoItem->preco_unitario     = number_format(Helper::formataDecimal($request['produto_preco_unitario'][$i]),2,'.','');
                    $pedidoItem->preco_venda        = number_format(Helper::formataDecimal($request['produto_preco_venda'][$i]),2,'.','');
                    $pedidoItem->valor_desconto     = $desconto;
                    $pedidoItem->valor_acrescimo    = $acrescimo;
                    $pedidoItem->valor_total        = number_format(Helper::formataDecimal($request['produto_preco_total'][$i]),2,'.','');
                    $pedidoItem->created_at         = new \DateTime();
                    $pedidoItem->updated_at         = new \DateTime();
                    $pedidoItem->save();


                    //registra o empenho da quantidade do lote
                    LoteController::atualizaQuantidadeEmpenhada($lote->erp_id);
                }
            }


            //gera vex sync
            VexSyncController::adiciona(Helper::formataTenantId($this->empfilId), 'post',  $pedido->getTable(), $pedido->id,  $pedido->getWebservice('add')); // edit,get,delete: rest/ped_venda/$erp_id


            //gera log de saldo devedor para o cliente referente ao pedido
            CreditoClienteController::adiciona($pedido, $cliente);


            $log[]   = ['success' => 'Pedido cadastrado com sucesso'];

        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['pedido']  = isset($pedido) ? $pedido : null;
        return $response;
    }


    //chamada para visualizar pedido de venda
    public function visualiza($pedido_venda_id)
    {
        $success = true;
        $log     = [];


        $pedido = PedidoVenda::join('vx_glo_cli','vx_glo_cli.erp_id','=','vx_fat_pvenda.vxglocli_erp_id')
            ->select('vx_fat_pvenda.*','vx_glo_cli.vxfatvend_erp_id_1','vx_glo_cli.vxfatvend_erp_id_2')
            ->where('id',$pedido_venda_id)
            ->where(function($query){

                $query->where('vx_fat_pvenda.vxgloempfil_id',$this->empfilId);
                $query->orWhere('vx_fat_pvenda.vxgloempfil_id','=',null);

            })->where(function($query){

                if((int)Auth::user()->vxwebperfil_id !== 1)
                {
                    $query->where('vx_fat_pvenda.vxfatvend_erp_id',$this->vendedorErpId);
                    $query->orWhere('vx_glo_cli.vxfatvend_erp_id_1',Auth::user()->vendedor->erp_id);
                    $query->orWhere('vx_glo_cli.vxfatvend_erp_id_2',Auth::user()->vendedor->erp_id);
                }

            })->first();


        if(!isset($pedido))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            //busca os clientes cadastrados
            $clientes = Cliente::where(function($query){

                $query->where('vxgloempfil_id',$this->empfilId);
                $query->orWhere('vxgloempfil_id','=',null);

            })->where(function($query) use ($pedido){

                $query->where('status','1');
                $query->orWhere('id','=',json_decode($pedido->cliente_data)->id);

            })->where(function($query){

                if((int)Auth::user()->vxwebperfil_id !== 1)
                {
                    $query->where('vxfatvend_erp_id_1',Auth::user()->vendedor->erp_id);
                    $query->orWhere('vxfatvend_erp_id_2',Auth::user()->vendedor->erp_id);
                }

            })->orderBy('razao_social','asc')->get();

            if(count($clientes) == 0)
            {
                $success = false;
                $log[]   = ['error' => 'Não é possível gerar um novo pedido, pois não há clientes ativos cadastrados'];
            }


            // Busca os produtos cadastrados
            $produtos = Produto::where(function($query){

                $query->where('vxgloempfil_id',$this->empfilId);
                $query->orWhere('vxgloempfil_id','=',null);

            })->where('status','1')->orderBy('descricao','asc')->get();

            if(count($produtos) == 0)
            {
                $success = false;
                $log[]   = ['error' => 'Não é possível gerar um novo pedido, pois não há produtos cadastrados'];
            }
            else
            {
                $itens = [];

                //verifica se os produtos possuem saldo em estoque
                foreach($produtos as $produto)
                {
                    $lote = Lote::where('vxgloprod_id',$produto->id)
                        ->where('saldo','>','0')
                        ->where('dt_valid','!=',null)
                        ->where('dt_valid','>=',Carbon::now()->addDays(1)->format('Y-m-d'))
                        ->first();

                    if(isset($lote))
                    {
                        //caso o pedido não esteja sincronizado, verificamos se o saldo ainda é maior que a quantidade empenhada
                        if($pedido->erp_id == null)
                        {
                            $empenho = LoteController::confirmaQuantidadeEmpenhada($lote->erp_id, $pedido->id)['empenho'];

                            $lote->saldo = $lote->saldo - $empenho;
                        }

                        if($lote->saldo > 0)
                        {
                            $itens[] = $produto;
                        }
                    }
                    else
                    {
                        //verifica se o item que não possui saldo, está cadastrado no pedido
                        $item = PedidoItem::where('vxfatpvenda_id',$pedido_venda_id)->where('vxgloprod_erp_id',$produto->erp_id)->first();

                        if(isset($item))
                        {
                            $itens[] = $produto;
                        }
                    }
                }

                $produtos = $itens;

            }

            // Busca as tabelas de preços cadastradas
            $tabelas = TabelaPreco::where(function($query){

                $query->where('vxgloempfil_id',$this->empfilId);
                $query->orWhere('vxgloempfil_id','=',null);

            })->orderBy('descricao','asc')->get();

            if(count($tabelas) == 0)
            {
                $success = false;
                $log[]   = ['error' => 'Não é possível gerar um novo pedido, pois não há tabelas de preços cadastradas'];
            }

            // Busca as condições de pagamento cadastradas
            $condicoes = CondicaoPagamento::where('vxgloempfil_id',$this->empfilId)
                ->where('web','1')
                ->where(function($query) use ($pedido){

                    $query->where('status','1');
                    $query->orWhere('erp_id','=',$pedido->vxglocpgto_erp_id);

                })
                ->orderBy('descricao','asc')
                ->get();

            if(count($condicoes) == 0)
            {
                $success = false;
                $log[]   = ['error' => 'Não é possível gerar um novo pedido, pois não é condições de pagamento ativas cadastradas'];
            }


            //busca classificação de risco para verificar máximo de desconto permitido
            $riscos = Risco::all();


            //busca os itens do pedido de venda
            $itens = PedidoItem::where('vxfatpvenda_id',$pedido_venda_id)->get();

        }


        $response['success']   = $success;
        $response['log']       = $log;
        $response['pedido']    = isset($pedido) ? $pedido : null;
        $response['itens']     = isset($itens) ? $itens : [];
        $response['clientes']  = isset($clientes) ? $clientes : [];
        $response['produtos']  = isset($produtos) ? $produtos : [];
        $response['tabelas']   = isset($tabelas) ? $tabelas : [];
        $response['condicoes'] = isset($condicoes) ? $condicoes : [];
        $response['riscos']    = isset($riscos) ? $riscos : [];
        return $response;
    }



    //post para editar pedido de venda
    public function editaPost(Request $request, $pedido_venda_id)
    {
        $success = true;
        $log     = [];

        $pedido = PedidoVenda::where('id',$pedido_venda_id)->where(function($query){

            $query->where('vxgloempfil_id',$this->empfilId);
            $query->orWhere('vxgloempfil_id','=',null);

        })->first();

        if(!isset($pedido))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            //verificamos se o pedido já foi processado no ERP
            $liberado = true;

            try
            {
                if($pedido->erp_id !== null)
                {
                    $assinatura = Assinatura::first();

                    $guzzle  = new Client();
                    $result  = $guzzle->request('GET', $assinatura->webservice_base . $pedido->getWebservice() . $pedido->erp_id);
                    $result  = json_decode($result->getBody());

                    $result = Helper::retornoERP($result->result);
                    $result = json_decode($result, true);


                    if($result['situacao_pedido'] !== 'A' and $result['situacao_pedido'] !== 'S')
                    {
                        $liberado = false;
                        $success  = false;
                        $log[]    = ['error' => 'Não foi possível editar o pedido, pois este já se encontra fechado'];

                        //atualizamos os dados do pedido e dos itens, de acordo com o ERP
                        \App\Http\Controllers\Erp\PedidoVendaController::update($result);
                    }

                }

            }
            catch(\Exception $e)
            {
                $liberado = false;
                $success  = false;
                $log[]    = ['error' => 'Não foi possível editar o pedido. Por favor acione a equipe de suporte'];
            }

            if($liberado)
            {


                //busca os dados para incluir o ERP ID
                $cliente  = Cliente::find($request['vxglocli_id']);
                $condicao = CondicaoPagamento::find($request['vxglocpgto_id']);
                $vendedor = Vendedor::find($this->vendedorId);

                $pedido->vxgloempfil_id      = $this->empfilId;
                $pedido->vxglocli_erp_id     = $cliente->erp_id;
                $pedido->vxglocpgto_erp_id   = $condicao->erp_id;
                $pedido->vxfatvend_erp_id    = $vendedor->erp_id;
                $pedido->cliente_data        = json_encode($cliente, JSON_UNESCAPED_UNICODE);
                $pedido->data_entrega        = isset($request['data_entrega']) ? Carbon::createFromFormat('d/m/Y',$request['data_entrega'])->format('Y-m-d') : null;
                $pedido->status_entrega      = $request['status_entrega'];
                $pedido->observacao          = isset($request['observacao']) ? $request['observacao'] : '';
                $pedido->obs_interna         = isset($request['obs_interna']) ? $request['obs_interna'] : '';
                $pedido->updated_at          = new \DateTime();
                $pedido->save();



                //exclui os itens de pedido que não foram enviados na requisição
                PedidoItem::where('vxfatpvenda_id',$pedido_venda_id)->forceDelete();

                if(isset($request['produto_id']))
                {
                    for($i = 0; $i < count($request['produto_id']); $i++)
                    {
                        $tabela  = TabelaPreco::find($request['produto_tabela_id'][$i]);

                        $produto = Produto::find($request['produto_id'][$i]);

                        $lote    = Lote::find($request['produto_lote_id'][$i]);

                        if((float) Helper::formataDecimal($request['produto_valor_desconto'][$i]) == 0.00)
                        {
                            $desconto  = 0.00;
                            $acrescimo = 0.00;
                        }
                        elseif((float) Helper::formataDecimal($request['produto_valor_desconto'][$i]) > 0.00)
                        {
                            $desconto  = (float) Helper::formataDecimal($request['produto_valor_desconto'][$i]);
                            $acrescimo = 0.00;
                        }
                        elseif((float) Helper::formataDecimal($request['produto_valor_desconto'][$i]) < 0.00)
                        {
                            $desconto  = 0.00;
                            $acrescimo = abs((float) Helper::formataDecimal($request['produto_valor_desconto'][$i]));
                        }

                        $pedidoItem = new PedidoItem();
                        $pedidoItem->vxfatpvenda_id     = $pedido->id;
                        $pedidoItem->vxgloprod_erp_id   = $produto->erp_id;
                        $pedidoItem->vxfattabprc_erp_id = $tabela->erp_id;
                        $pedidoItem->vxestarmz_erp_id   = $lote->armazem->erp_id;
                        $pedidoItem->vxestlote_erp_id   = $lote->erp_id;
                        $pedidoItem->quantidade         = $request['produto_quantidade'][$i];
                        $pedidoItem->produto_data       = json_encode($produto, JSON_UNESCAPED_UNICODE);
                        $pedidoItem->preco_unitario     = number_format(Helper::formataDecimal($request['produto_preco_unitario'][$i]),2,'.','');
                        $pedidoItem->preco_venda        = number_format(Helper::formataDecimal($request['produto_preco_venda'][$i]),2,'.','');
                        $pedidoItem->valor_desconto     = $desconto;
                        $pedidoItem->valor_acrescimo    = $acrescimo;
                        $pedidoItem->valor_total        = number_format(Helper::formataDecimal($request['produto_preco_total'][$i]),2,'.','');
                        $pedidoItem->created_at         = new \DateTime();
                        $pedidoItem->updated_at         = new \DateTime();
                        $pedidoItem->save();


                        //registra o empenho da quantidade do lote
                        LoteController::atualizaQuantidadeEmpenhada($lote->erp_id);
                    }
                }


                //gera vex sync
                if(isset($pedido->erp_id))
                {
                    VexSyncController::adiciona(Helper::formataTenantId($this->empfilId), 'put',  $pedido->getTable(), $pedido->id,  $pedido->getWebservice('edit/'.$pedido->erp_id));
                }
                elseif($pedido->situacao_pedido == 'S')
                {
                    VexSyncController::adiciona(Helper::formataTenantId($this->empfilId), 'post',  $pedido->getTable(), $pedido->id,  $pedido->getWebservice('add')); // edit,get,delete: rest/ped_venda/$erp_id
                }


                //atualiza log de saldo devedor para o cliente referente ao pedido
                CreditoClienteController::atualiza($pedido, $cliente);


                //após gerar o vex sync retornamos o status para "Aberto"
                $pedido->situacao_pedido = "A";
                $pedido->updated_at      = new \DateTime();
                $pedido->save();

                $log[]   = ['success' => 'Pedido atualizado com sucesso'];
            }
        }


        $response['success']   = $success;
        $response['log']       = $log;
        $response['pedido']    = isset($pedido) ? $pedido : null;
        return $response;
    }



    //post para excluir pedido de venda
    public function excluiPost(Request $request, $pedido_venda_id)
    {
        $success = true;
        $log     = [];

        $pedido = PedidoVenda::where('id',$pedido_venda_id)->where(function($query){

            $query->where('vxgloempfil_id',$this->empfilId);
            $query->orWhere('vxgloempfil_id','=',null);

        })->first();

        if(!isset($pedido))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            //verificamos se o pedido já foi processado no ERP
            $liberado = true;

            try
            {
                $assinatura = Assinatura::first();

                if($pedido->erp_id !== null)
                {
                    $guzzle  = new Client();
                    $result  = $guzzle->request('GET', $assinatura->webservice_base . $pedido->getWebservice() . $pedido->erp_id);
                    $result  = json_decode($result->getBody());

                    $result = Helper::retornoERP($result->result);
                    $result = json_decode($result, true);

                    if($result['situacao_pedido'] !== 'A' and $result['situacao_pedido'] !== 'S')
                    {
                        $liberado = false;
                        $success  = false;
                        $log[]    = ['error' => 'Não foi possível excluir o pedido, pois este já se encontra fechado'];

                        //atualizamos os dados do pedido e dos itens, de acordo com o ERP
                        \App\Http\Controllers\Erp\PedidoVendaController::update($result);
                    }
                }
            }
            catch(\Exception $e)
            {
                $liberado = false;
                $success  = false;
                $log[]    = ['error' => 'Não foi possível excluir o pedido. Por favor acione a equipe de suporte'];
            }


            if($liberado)
            {

                try
                {
                    $assinatura = Assinatura::first();

                    if($pedido->erp_id !== null)
                    {
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


                    //atualiza a quantidade empenhada referente ao lote de cada item a ser excluído
                    foreach(PedidoItem::where('vxfatpvenda_id',$pedido_venda_id)->get() as $item)
                    {
                        LoteController::atualizaQuantidadeEmpenhada($item->vxestlote_erp_id);
                    }

                    //exclui os itens do pedido em questão
                    PedidoItem::where('vxfatpvenda_id',$pedido_venda_id)->delete();


                    $log[]   = ['success' => 'Pedido excluído com sucesso'];
                }
            }
        }


        $response['success']   = $success;
        $response['log']       = $log;
        return $response;
    }



    //chamada para imprimir pedido de venda
    public function imprimePDF($pedido_venda_id)
    {
        $success = true;
        $log     = [];

        $pedido = PedidoVenda::join('vx_glo_cli','vx_glo_cli.erp_id','=','vx_fat_pvenda.vxglocli_erp_id')
            ->select('vx_fat_pvenda.*','vx_glo_cli.vxfatvend_erp_id_1','vx_glo_cli.vxfatvend_erp_id_2')
            ->where('id',$pedido_venda_id)
            ->where(function($query){

                $query->where('vx_fat_pvenda.vxgloempfil_id',$this->empfilId);
                $query->orWhere('vx_fat_pvenda.vxgloempfil_id','=',null);

            })->where(function($query){

                if((int)Auth::user()->vxwebperfil_id !== 1)
                {
                    $query->where('vx_fat_pvenda.vxfatvend_erp_id',$this->vendedorErpId);
                    $query->orWhere('vx_glo_cli.vxfatvend_erp_id_1',Auth::user()->vendedor->erp_id);
                    $query->orWhere('vx_glo_cli.vxfatvend_erp_id_2',Auth::user()->vendedor->erp_id);
                }

            })->first();


        if(!isset($pedido))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            try
            {
                $controller   = new ConfiguracaoController();
                $configuracao = $controller->visualiza();
                $configuracao = $configuracao['configuracao'];

                $pdf = new Main();
                $pdf->generate($pedido, $configuracao);
            }
            catch(\Exception $exception)
            {
                if(env('APP_DD') == true)
                {
                    dd($exception);
                }

                $success = false;
                $log[]   = ['error' => 'Não foi possível imprimir o PDF'];
            }

        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['pedido']  = isset($pedido) ? $pedido : null;
        return $response;
    }

}
