<?php 

namespace App\Http\Locators\Mobile; 

use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Mobile\ClienteController;

/**
 * @description Clientes
 * Class ClienteLocator
 * @package App\Http\Locators\Mobile
 */
class ClienteLocator extends Controller
{
    //construct
    public function __construct()
    {
        //
    }

    /**
     * @description Lista de clientes
     * @info Listagem de clientes via API
     * @return mixed
     */
    public function lista(Request $request)
    {
        $controller = new ClienteController($request->header('filial'));
        
        $response   = $controller->lista();

        return Helper::retornoMobile($response);
    }

    /**
     * @description Cadastro de clientes
     * @info Cadastro de clientes via API
     * @param Request $request
     * @return mixed
     */
    public function adicionaPost(Request $request)
    {
        $controller = new ClienteController($request->header('filial'));

        $response   = $controller->adicionaPost($request);

        return Helper::retornoMobile($response);
    }

    /**
     * @description Visualização de cliente
     * @info Obter dados de cliente via API
     * @param $cliente_id
     * @return mixed
     */
    public function visualiza($cliente_id)
    {
        $controller = new ClienteController($request->header('filial'));

        $response   = $controller->visualiza($cliente_id);

        return Helper::retornoMobile($response);
    }

    /**
     * @description Edição de cliente
     * @info Edição de dados de cliente via API
     * @param Request $request
     * @param $cliente_id
     * @return mixed
     */
    public function editaPost(Request $request, $cliente_id)
    {
        $controller = new ClienteController($request->header('filial'));

        $response   = $controller->editaPost($request, $cliente_id);

        return Helper::retornoMobile($response);
    }
}