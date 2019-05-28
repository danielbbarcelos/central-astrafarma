<?php 

namespace App\Http\Locators\Mobile; 

use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Mobile\TabelaPrecoController;

/**
 * @description Tabela de preços
 * Class TabelaPrecoLocator
 * @package App\Http\Locators\Mobile
 */
class TabelaPrecoLocator extends Controller
{
    //construct
    public function __construct()
    {
        //
    }

    /**
     * @description Lista de tabelas de preços
     * @info Listagem de tabelas de preços via API
     * @param $produto_id
     * @return mixed
     */
    public function lista(Request $request)
    {
        $controller = new TabelaPrecoController($request->header('filial'));

        $response   = $controller->lista($request);

        return Helper::retornoMobile($response);
    }

    /**
     * @description Visualização de tabela de preço
     * @info Obter dados de uma tabela de preço via API
     * @param $preco_produto_id
     * @return mixed
     */
    public function visualiza(Request $request, $tabela_preco_id, $uf)
    {
        $controller = new TabelaPrecoController($request->header('filial'));

        $response   = $controller->visualiza($request, $tabela_preco_id, $uf);

        return Helper::retornoMobile($response);
    }
}