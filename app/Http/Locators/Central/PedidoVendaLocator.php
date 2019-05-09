<?php

namespace App\Http\Locators\Central;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller;
use App\Http\Controllers\Central\PedidoVendaController;

/**
 * @description Pedido de venda
 * Class PedidoVendaLocator
 * @package App\Http\Locators\Central
 */
class PedidoVendaLocator extends Controller
{

    //path's name of resources/views
    protected $basePathViews = 'pages.pedidos-vendas.';


    //construct
    public function __construct()
    {
        $this->middleware('permissions', [ 'except' => []]);
    }


    /**
     * @description Tela de configuração de pedidos de venda
     * @info Tela de configuração geral de pedidos de venda na CentralVEX
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function configuracao()
    {
        $controller = new PedidoVendaController();

        $response   = $controller->configuracao();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'configuracao', $response);
    }

    /**
     * @description Configuração de pedidos de venda
     * @info Configuração de pedidos de venda na CentralVEX
     * @param Request $request
     * @return mixed
     */
    public function configuracaoPost(Request $request)
    {
        $controller = new PedidoVendaController();

        $response   = $controller->configuracaoPost($request);

        return Redirect::back()->withInput()->with('log',$response['log']);

    }


    /**
     * @description Listagem de pedidos de venda
     * @info Listagem de pedidos de venda na CentralVEX
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lista()
    {
        $controller = new PedidoVendaController();

        $response   = $controller->lista();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'lista', $response);
    }


    /**
     * @description Tela de geração de pedidos de venda
     * @info Tela de geração de pedidos de venda na CentralVEX
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adiciona()
    {
        $controller = new PedidoVendaController();

        $response   = $controller->adiciona();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'adiciona', $response);
    }


    /**
     * @description Geração de pedidos de venda
     * @info Geração de pedidos de venda na CentralVEX
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function adicionaPost(Request $request)
    {
        $controller = new PedidoVendaController();

        $response   = $controller->adicionaPost($request);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/pedidos-vendas')->withInput()->with('log',$response['log']);
    }


    /**
     * @description Tela de visualização de pedido de venda
     * @info Tela para visualizar pedido de venda na CentralVEX
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function visualiza($pedido_venda_id)
    {
        $controller = new PedidoVendaController();

        $response   = $controller->visualiza($pedido_venda_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'pedido-venda', $response);
    }

    /**
     * @description Edição de pedido de venda
     * @info Edição pedido de venda na CentralVEX
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editaPost(Request $request, $pedido_venda_id)
    {
        $controller = new PedidoVendaController();

        $response   = $controller->editaPost($request, $pedido_venda_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/pedidos-vendas')->withInput()->with('log',$response['log']);
    }


    /**
     * @description Impressão de pedidos de venda
     * @info Geração de PDF para impressão de pedidos de venda na CentralVEX
     * @param $garantia_id
     * @return mixed
     */
    public function imprimePDF($pedido_venda_id)
    {
        $controller = new PedidoVendaController();

        $response   = $controller->imprimePDF($pedido_venda_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        //
    }


}