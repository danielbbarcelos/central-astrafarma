<?php 

namespace App\Http\Locators\Mobile; 

use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Mobile\ProdutoController;

/**
 * @description Produtos
 * Class ProdutoLocator
 * @package App\Http\Locators\Mobile
 */
class ProdutoLocator extends Controller
{
    //construct
    public function __construct()
    {
        //
    }

    /**
     * @description Lista de produtos
     * @info Listagem de produtos via API
     * @return mixed
     */
    public function lista(Request $request)
    {
        $controller = new ProdutoController($request->header('filial'));

        $response   = $controller->lista($request);

        return Helper::retornoMobile($response);
    }

    /**
     * @description Visualização de produto
     * @info Obter dados de um produto via API
     * @param $produto_id
     * @return mixed
     */
    public function visualiza(Request $request, $produto_id)
    {
        $controller = new ProdutoController();

        $response   = $controller->visualiza($request, $produto_id);

        return Helper::retornoMobile($response);
    }
}