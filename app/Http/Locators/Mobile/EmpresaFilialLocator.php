<?php 

namespace App\Http\Locators\Mobile; 

use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Mobile\EmpresaFilialController;

/**
 * @description EmpresaFilial
 * Class EmpresaFilialLocator
 * @package App\Http\Locators\Mobile
 */
class EmpresaFilialLocator extends Controller
{
    //construct
    public function __construct()
    {
        //
    }

    /**
     * @description Lista de filiais
     * @info Listagem de filiais via API
     * @return mixed
     */
    public function lista()
    {
        $controller = new EmpresaFilialController();

        $response   = $controller->lista();

        return Helper::retornoMobile($response);
    }

    /**
     * @description Visualização de filial
     * @info Obter dados de uma filial via API
     * @param $empresa_filial_id
     * @return mixed
     */
    public function visualiza($empresa_filial_id)
    {
        $controller = new EmpresaFilialController();

        $response   = $controller->visualiza($empresa_filial_id);

        return Helper::retornoMobile($response);
    }
}