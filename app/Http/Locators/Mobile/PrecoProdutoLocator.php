<?php 

namespace App\Http\Locators\Mobile; 

use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Mobile\PrecoProdutoController;

/**
 * @description Tabela de preços
 * Class PrecoProdutoLocator
 * @package App\Http\Locators\Mobile
 */
class PrecoProdutoLocator extends Controller
{
    //construct
    public function __construct()
    {
        //
    }

    /**
     * @description Lista de tabelas de preço por produto
     * @info Listagem de tabelas de preço por produto desejado via API
     * @param $produto_id
     * @return mixed
     */
    public function listaPorProduto($produto_id)
    {
        $controller = new PrecoProdutoController();

        $response   = $controller->listaPorProduto($produto_id);

        return Helper::retornoMobile($response);
    }

    /**
     * @description Visualização de tabela de preço
     * @info Obter dados de uma tabela de preço via API
     * @param $preco_produto_id
     * @return mixed
     */
    public function visualiza($preco_produto_id)
    {
        $controller = new PrecoProdutoController();

        $response   = $controller->visualiza($preco_produto_id);

        return Helper::retornoMobile($response);
    }
}