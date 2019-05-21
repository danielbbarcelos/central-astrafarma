<?php 

namespace App\Http\Locators\Mobile;

use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Mobile\CondicaoPagamentoController;

/**
 * @description Condições de pagamento
 * Class CondicaoPagamentoLocator
 * @package App\Http\Locators\Mobile
 */
class CondicaoPagamentoLocator extends Controller
{
    //construct
    public function __construct()
    {
        //
    }

    /**
     * @description Lista de condições de pagamento
     * @info Listagem de condições de pagamento via API
     * @return mixed
     */
    public function lista(Request $request)
    {
        $controller = new CondicaoPagamentoController($request->header('filial'));

        $response   = $controller->lista();

        return Helper::retornoMobile($response);
    }
}