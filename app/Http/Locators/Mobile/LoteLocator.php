<?php 

namespace App\Http\Locators\Mobile; 

use App\Http\Controllers\Mobile\LoteController;
use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Mobile\ClienteController;

/**
 * @description Lote
 * Class LoteLocator
 * @package App\Http\Locators\Mobile
 */
class LoteLocator extends Controller
{
    //construct
    public function __construct()
    {
        //
    }

    /**
     * @description Lista de lotes
     * @info Listagem de lotes via API
     * @param $produto_id, $tabela_preco_id
     * @return mixed
     */
    public function lista(Request $request, $produto_id, $tabela_preco_id, $pedido_id = null)
    {
        $controller = new LoteController($request->header('filial'));

        $response   = $controller->lista($request, $produto_id, $tabela_preco_id, $pedido_id);

        return Helper::retornoMobile($response);
    }

    /**
     * @description Cálcula itens por lote
     * @info Retorna itens por lote de acordo com os parâmetros do item no pedido, via API
     * @param Request $request
     * @return mixed
     */
    public function calculaPorItemPost(Request $request)
    {
        $controller = new LoteController($request->header('filial'));

        $response   = $controller->calculaPorItemPost($request);

        return Helper::retornoMobile($response);
    }

}