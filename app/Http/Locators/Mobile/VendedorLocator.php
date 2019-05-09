<?php 

namespace App\Http\Locators\Mobile; 

use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Mobile\VendedorController;

/**
 * @description Vendedores
 * Class VendedorLocator
 * @package App\Http\Locators\Mobile
 */
class VendedorLocator extends Controller
{
    //construct
    public function __construct()
    {
        //
    }

    /**
     * @description Lista de vendedores
     * @info Listagem de vendedores via API
     * @return mixed
     */
    public function lista()
    {
        $controller = new VendedorController();

        $response   = $controller->lista();

        return Helper::retornoMobile($response);
    }

    /**
     * @description Visualização de vendedor
     * @info Obter dados de um vendedor via API
     * @param $vendedor_id
     * @return mixed
     */
    public function visualiza($vendedor_id)
    {
        $controller = new VendedorController();

        $response   = $controller->visualiza($vendedor_id);

        return Helper::retornoMobile($response);
    }
}