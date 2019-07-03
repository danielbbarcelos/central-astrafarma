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