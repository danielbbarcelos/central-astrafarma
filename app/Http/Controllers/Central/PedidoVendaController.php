<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
use App\Assinatura;
use App\Cliente;
use App\CondicaoPagamento;
use App\Files\PedidoVenda\Main;
use App\Http\Controllers\Mobile\VexSyncController;
use App\PedidoItem;
use App\PedidoVenda;
use App\PedidoVendaConfiguracao;

//mails

//framework
use App\Http\Controllers\Controller;
use App\PrecoProduto;
use App\Produto;
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

    //construct
    public function __construct()
    {
        $this->empfilId   = Auth::user()->userEmpresaFilial->empfil->id;
        $this->vendedorId = isset(Auth::user()->vendedor) ? Auth::user()->vendedor->id : '1';
    }


    //chamada da tela para configurar pedidos de venda
    public function configuracao()
    {
        $success = true;
        $log     = [];

        $configuracao = PedidoVendaConfiguracao::where('vxgloempfil_id',$this->empfilId)->first();

        if(!isset($configuracao))
        {
            $configuracao = new PedidoVendaConfiguracao();
            $configuracao->vxgloempfil_id   = $this->empfilId;
            $configuracao->created_at       = new \DateTime();
            $configuracao->updated_at       = new \DateTime();
            $configuracao->save();
        }

        $response['success']      = $success;
        $response['log']          = $log;
        $response['configuracao'] = $configuracao;
        return $response;
    }

    //post para configurar pedidos de venda
    public function configuracaoPost(Request $request)
    {
        $success = true;
        $log     = [];

        $configuracao = PedidoVendaConfiguracao::where('vxgloempfil_id',$this->empfilId)->first();

        if(!isset($configuracao))
        {
            $configuracao = new PedidoVendaConfiguracao();
            $configuracao->created_at = new \DateTime();
        }

        $configuracao->pdf_template = $request['pdf_template'];
        $configuracao->pdf_logo     = $request['pdf_logo']; //tratar upload da logo
        $configuracao->updated_at   = new \DateTime();
        $configuracao->save();

        $log[] = ['success' => 'Configurações atualizadas com sucesso'];

        $response['success']      = $success;
        $response['log']          = $log;
        $response['configuracao'] = $configuracao;
        return $response;
    }


    //retorna array do objeto
    public function lista()
    {
        $success = true;
        $log     = [];

        $pedidos = PedidoVenda::where(function($query){

            $query->where('vxgloempfil_id',$this->empfilId);
            $query->orWhere('vxgloempfil_id','=',null);

        })->orderBy('updated_at','desc')->get();

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

        $clientes = Cliente::where(function($query){

            $query->where('vxgloempfil_id',$this->empfilId);
            $query->orWhere('vxgloempfil_id','=',null);

        })->where('status','1')->orderBy('razao_social','asc')->get();

        if(count($clientes) == 0)
        {
            $success = false;
            $log[]   = ['error' => 'Não é possível gerar um novo pedido, pois não há clientes ativos cadastrados'];
        }


        $tabelas = [];

        $precos  = PrecoProduto::where(function($query){

            $query->where('vxgloempfil_id',$this->empfilId);
            $query->orWhere('vxgloempfil_id','=',null);

        })->orderBy('descricao','asc')->get();

        if(count($precos) == 0)
        {
            $success = false;
            $log[]   = ['error' => 'Não é possível gerar um novo pedido, pois não há tabelas de preços cadastradas'];
        }
        else
        {
            foreach($precos as $preco)
            {
                $tabelaPrecoProduto = TabelaPrecoProduto::where('vxfattabprc_id',$preco->id)->get();

                $produtos = [];

                foreach($tabelaPrecoProduto as $item)
                {
                    $produto = Produto::find($item->vxgloprod_id);

                    if(isset($produto))
                    {
                        $produto->uf             = $item->uf;
                        $produto->preco_venda    = $item->preco_venda;
                        $produto->preco_maximo   = $item->preco_maximo;
                        $produto->valor_desconto = $item->valor_desconto;
                        $produto->fator          = $item->fator;

                        $produtos[] = $produto;
                    }
                }

                if(count($produtos) > 0)
                {
                    $preco->produtos = $produtos;

                    $tabelas[] = $preco;
                }
            }
        }


        if(count($tabelas) == 0)
        {
            $success = false;
            $log[]   = ['error' => 'Não é possível gerar um novo pedido, pois não há produtos vincualdos as tabelas de preços'];
        }

        $condicoes = CondicaoPagamento::where('vxgloempfil_id',$this->empfilId)->where('status','1')->orderBy('descricao','asc')->get();

        if(count($condicoes) == 0)
        {
            $success = false;
            $log[]   = ['error' => 'Não é possível gerar um novo pedido, pois não é condições de pagamento ativas cadastradas'];
        }

        $response['success']   = $success;
        $response['log']       = $log;
        $response['pedido']    = $pedido;
        $response['clientes']  = $clientes;
        $response['tabelas']   = $tabelas;
        $response['condicoes'] = $condicoes;
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
            $preco    = PrecoProduto::find($request['vxfattabprc_id']);


            $pedido = new PedidoVenda();
            $pedido->situacao_pedido     = "A";
            $pedido->vxgloempfil_id      = $this->empfilId;
            $pedido->vxglocli_erp_id     = $cliente->erp_id;
            $pedido->vxglocpgto_erp_id   = $condicao->erp_id;
            $pedido->vxfatvend_erp_id    = $vendedor->erp_id;
            $pedido->vxfattabprc_erp_id  = $preco->erp_id;
            $pedido->cliente_data        = json_encode($cliente, JSON_UNESCAPED_UNICODE);
            $pedido->data_entrega        = isset($request['data_entrega']) ? Carbon::createFromFormat('d/m/Y',$request['data_entrega'])->format('Y-m-d') : null;
            $pedido->observacao          = isset($request['observacao']) ? $request['observacao'] : '';
            $pedido->created_at          = new \DateTime();
            $pedido->updated_at          = new \DateTime();
            $pedido->save();

            if(isset($request['produto_id']))
            {
                for($i = 0; $i < count($request['produto_id']); $i++)
                {
                    $produto = Produto::find($request['produto_id'][$i]);

                    $pedidoItem = new PedidoItem();
                    $pedidoItem->vxfatpvenda_id   = $pedido->id;
                    $pedidoItem->vxgloprod_erp_id = $produto->erp_id;
                    $pedidoItem->produto_data     = json_encode($produto, JSON_UNESCAPED_UNICODE);
                    $pedidoItem->quantidade       = $request['produto_quantidade'][$i];
                    $pedidoItem->preco_unitario   = number_format(Helper::formataDecimal($request['produto_preco_unitario'][$i]),2,'.','');
                    $pedidoItem->preco_venda      = number_format(Helper::formataDecimal($request['produto_preco_venda'][$i]),2,'.','');
                    $pedidoItem->valor_desconto   = number_format(Helper::formataDecimal($request['produto_valor_desconto'][$i]),2,'.','');
                    $pedidoItem->valor_total      = number_format(Helper::formataDecimal($request['produto_preco_total'][$i]),2,'.','');
                    $pedidoItem->created_at       = new \DateTime();
                    $pedidoItem->updated_at       = new \DateTime();
                    $pedidoItem->save();
                }
            }

            //gera vex sync
            VexSyncController::adiciona(Helper::formataTenantId($this->empfilId), 'post',  $pedido->getTable(), $pedido->id,  $pedido->getWebservice('add')); // edit,get,delete: rest/ped_venda/$erp_id

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
            //busca os itens do pedido de venda
            $itens = PedidoItem::where('vxfatpvenda_id',$pedido_venda_id)->get();

            //busca os clientes cadastrados
            $clientes = Cliente::where(function($query){

                $query->where('vxgloempfil_id',$this->empfilId);
                $query->orWhere('vxgloempfil_id','=',null);

            })->where(function($query) use ($pedido){

                $query->where('status','1');
                $query->orWhere('id','=',json_decode($pedido->cliente_data)->id);

            })->orderBy('razao_social','asc')->get();

            if(count($clientes) == 0)
            {
                $success = false;
                $log[]   = ['error' => 'Não é possível gerar um novo pedido, pois não há clientes ativos cadastrados'];
            }


            //busca as tabelas de preço cadastradas
            $tabelas = [];

            $precos  = PrecoProduto::where(function($query){

                $query->where('vxgloempfil_id',$this->empfilId);
                $query->orWhere('vxgloempfil_id','=',null);

            })->where(function($query) use ($pedido){

                $query->orWhere('erp_id','=',$pedido->vxfattabprc_erp_id);

            })->orderBy('descricao','asc')->get();


            foreach($precos as $preco)
            {
                $tabelaPrecoProduto = TabelaPrecoProduto::where('vxfattabprc_id',$preco->id)->get();

                $produtos = [];

                foreach($tabelaPrecoProduto as $item)
                {
                    $produto = Produto::find($item->vxgloprod_id);

                    if(isset($produto))
                    {
                        $produto->uf             = $item->uf;
                        $produto->preco_venda    = $item->preco_venda;
                        $produto->preco_maximo   = $item->preco_maximo;
                        $produto->valor_desconto = $item->valor_desconto;
                        $produto->fator          = $item->fator;

                        $produtos[] = $produto;
                    }
                }

                if(count($produtos) > 0)
                {
                    $preco->produtos = $produtos;

                    $tabelas[] = $preco;
                }
            }


            $condicoes = CondicaoPagamento::where('vxgloempfil_id',$this->empfilId)->where(function($query) use ($pedido){

                $query->where('status','1');
                $query->orWhere('erp_id','=',$pedido->vxglocpgto_erp_id);

            })->orderBy('descricao','asc')->get();

        }


        $response['success']   = $success;
        $response['log']       = $log;
        $response['pedido']    = isset($pedido) ? $pedido : null;
        $response['itens']     = isset($itens) ? $itens : [];
        $response['clientes']  = isset($clientes) ? $clientes : [];
        $response['tabelas']   = isset($tabelas) ? $tabelas : [];
        $response['condicoes'] = isset($condicoes) ? $condicoes : [];
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


                    if($result['situacao_pedido'] !== 'A')
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
                $preco    = PrecoProduto::find($request['vxfattabprc_id']);

                $pedido->vxgloempfil_id      = $this->empfilId;
                $pedido->vxglocli_erp_id     = $cliente->erp_id;
                $pedido->vxglocpgto_erp_id   = $condicao->erp_id;
                $pedido->vxfatvend_erp_id    = $vendedor->erp_id;
                $pedido->vxfattabprc_erp_id  = $preco->erp_id;
                $pedido->cliente_data        = json_encode($cliente, JSON_UNESCAPED_UNICODE);
                $pedido->data_entrega        = isset($request['data_entrega']) ? Carbon::createFromFormat('d/m/Y',$request['data_entrega'])->format('Y-m-d') : null;
                $pedido->observacao          = isset($request['observacao']) ? $request['observacao'] : '';
                $pedido->updated_at          = new \DateTime();
                $pedido->save();

                //exclui os itens de pedido que não foram enviados na requisição
                PedidoItem::where('vxfatpvenda_id',$pedido_venda_id)->delete();

                if(isset($request['produto_id']))
                {
                    for($i = 0; $i < count($request['produto_id']); $i++)
                    {
                        $produto = Produto::find($request['produto_id'][$i]);

                        $pedidoItem = new PedidoItem();
                        $pedidoItem->vxfatpvenda_id   = $pedido->id;
                        $pedidoItem->vxgloprod_erp_id = $produto->erp_id;
                        $pedidoItem->produto_data     = json_encode($produto, JSON_UNESCAPED_UNICODE);
                        $pedidoItem->quantidade       = $request['produto_quantidade'][$i];
                        $pedidoItem->preco_unitario   = number_format(Helper::formataDecimal($request['produto_preco_unitario'][$i]),2,'.','');
                        $pedidoItem->preco_venda      = number_format(Helper::formataDecimal($request['produto_preco_venda'][$i]),2,'.','');
                        $pedidoItem->valor_desconto   = number_format(Helper::formataDecimal($request['produto_valor_desconto'][$i]),2,'.','');
                        $pedidoItem->valor_total      = number_format(Helper::formataDecimal($request['produto_preco_total'][$i]),2,'.','');
                        $pedidoItem->created_at       = new \DateTime();
                        $pedidoItem->updated_at       = new \DateTime();
                        $pedidoItem->save();
                    }
                }

                //gera vex sync
                if(isset($pedido->erp_id))
                {
                    VexSyncController::adiciona(Helper::formataTenantId($this->empfilId), 'put',  $pedido->getTable(), $pedido->id,  $pedido->getWebservice('edit/'.$pedido->erp_id));
                }

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

        })->where('erp_id','!=',null)->first();

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

                $guzzle  = new Client();
                $result  = $guzzle->request('GET', $assinatura->webservice_base . $pedido->getWebservice() . $pedido->erp_id);
                $result  = json_decode($result->getBody());

                $result = Helper::retornoERP($result->result);
                $result = json_decode($result, true);


                if($result['situacao_pedido'] !== 'A')
                {
                    $liberado = false;
                    $success  = false;
                    $log[]    = ['error' => 'Não foi possível excluir o pedido, pois este já se encontra fechado'];

                    //atualizamos os dados do pedido e dos itens, de acordo com o ERP
                    \App\Http\Controllers\Erp\PedidoVendaController::update($result);
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

                    $guzzle  = new Client();
                    $result  = $guzzle->request('DELETE', $assinatura->webservice_base . $pedido->getWebservice() . 'delete/'. $pedido->erp_id, [
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

        $pedido = PedidoVenda::find($pedido_venda_id);

        if(!isset($pedido))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            try
            {
                $configuracao = $this->configuracao();
                $configuracao = $configuracao['configuracao'];

                $pdf = new Main();
                $pdf->generate($pedido, $configuracao);
            }
            catch(\Exception $exception)
            {
                $success = false;
                $log[]   = 'Não foi possível imprimir o PDF';
            }

        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['pedido']  = isset($pedido) ? $pedido : null;
        return $response;
    }

}