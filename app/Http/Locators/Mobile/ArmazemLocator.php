<?php 

namespace App\Http\Locators\Mobile; 

use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Mobile\ArmazemController;

/**
 * @description Controle de armazéns
 * Class ArmazemLocator
 * @package App\Http\Locators\Mobile
 */
class ArmazemLocator extends Controller
{
    //construct
    public function __construct()
    {
        //
    }

    /**
     * @description Lista de armazéns
     * @info Listagem de armazéns via API
     * @return mixed
     */
    public function lista()
    {
        $controller = new ArmazemController();

        $response   = $controller->lista();

        return Helper::retornoMobile($response);
    }

    /**
     * @description Visualização de armazém
     * @info Obter dados de um armazém via API
     * @param $armazem_id
     * @return mixed
     */
    public function visualiza($armazem_id)
    {
        $controller = new ArmazemController();

        $response   = $controller->visualiza($armazem_id);

        return Helper::retornoMobile($response);
    }
}