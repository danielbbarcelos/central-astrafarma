<?php 

namespace App\Http\Locators\Mobile; 

use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Mobile\PedidoVendaController;

/**
 * @description Pedidos de venda
 * Class PedidoVendaLocator
 * @package App\Http\Locators\Mobile
 */
class PedidoVendaLocator extends Controller
{
    //construct
    public function __construct()
    {
        //
    }

    /**
     * @description Lista de pedidos de venda
     * @info Listagem de pedidos de venda cadastrado através da plataforma VEX
     * @return mixed
     */
    public function lista(Request $request)
    {
        $controller = new PedidoVendaController($request->header('filial'), Helper::JWTAuthorization($request->header('Authorization')));
        
        $response   = $controller->lista($request);

        return Helper::retornoMobile($response);
    }

    /**
     * @description Cadastrar pedido de venda
     * @info Cadastro de pedido de venda via API
     * @param Request $request
     * @return mixed
     */
    public function adicionaPost(Request $request)
    {
        $controller = new PedidoVendaController($request->header('filial'), Helper::JWTAuthorization($request->header('Authorization')));

        $response   = $controller->adicionaPost($request);

        return Helper::retornoMobile($response);
    }

    /**
     * @description Edição de pedido de venda
     * @info Edição de dados de pedido de venda via API
     * @param Request $request
     * @param $cliente_id
     * @return mixed
     */
    public function editaPost(Request $request, $pedido_id)
    {
        $controller = new PedidoVendaController($request->header('filial'), Helper::JWTAuthorization($request->header('Authorization')));

        $response   = $controller->editaPost($request, $pedido_id);

        return Helper::retornoMobile($response);
    }

    /**
     * @description Visualização de pedido de venda
     * @info Obter dados de pedido de venda via API
     * @param $pedido_id
     * @return mixed
     */
    public function visualiza(Request $request, $pedido_id)
    {
        $controller = new PedidoVendaController($request->header('filial'), Helper::JWTAuthorization($request->header('Authorization')));

        $response   = $controller->visualiza($pedido_id);

        return Helper::retornoMobile($response);
    }


    /**
     * @description Exclusão de pedido de venda
     * @info Exclusão de pedido de venda via API
     * @param Request $request
     * @param $cliente_id
     * @return mixed
     */
    public function excluiPost(Request $request, $pedido_id)
    {
        $controller = new PedidoVendaController($request->header('filial'), Helper::JWTAuthorization($request->header('Authorization')));

        $response   = $controller->excluiPost($request, $pedido_id);

        return Helper::retornoMobile($response);
    }
}