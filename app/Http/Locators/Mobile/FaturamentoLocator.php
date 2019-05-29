<?php 

namespace App\Http\Locators\Mobile; 

use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Mobile\FaturamentoController;
use JWTAuth;
/**
 * @description Faturamento
 * Class FaturamentoLocator
 * @package App\Http\Locators\Mobile
 */
class FaturamentoLocator extends Controller
{
    //construct
    public function __construct()
    {
        //
    }

    /**
     * @description Dashboard do módulo faturamento
     * @info Busca de dados para dinamizar dashboard do módulo faturamento via API
     * @return mixed
     */
    public function dashboard(Request $request)
    {
        $controller = new FaturamentoController($request->header('filial'), Helper::JWTAuthorization($request->header('Authorization')));
        
        $response   = $controller->dashboard($request);

        return Helper::retornoMobile($response);
    }

}