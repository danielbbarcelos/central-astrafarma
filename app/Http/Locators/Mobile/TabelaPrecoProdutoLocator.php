<?php 

namespace App\Http\Locators\Mobile; 

use App\Http\Controllers\Mobile\TabelaPrecoProdutoController;
use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Mobile\TabelaPrecoController;

/**
 * @description Tabela de preços por produto
 * Class TabelaPrecoProdutoLocator
 * @package App\Http\Locators\Mobile
 */
class TabelaPrecoProdutoLocator extends Controller
{
    //construct
    public function __construct()
    {
        //
    }

    /**
     * @description Lista de preços por produto
     * @info Listagem de preços com base na tabela, produto e UF selecionada, via API
     * @param $tabela_id, $uf, $produto_id
     * @return mixed
     */
    public function busca(Request $request, $tabela_id, $uf, $produto_id)
    {
        $controller = new TabelaPrecoProdutoController($request->header('filial'));

        $response   = $controller->busca($tabela_id, $uf, $produto_id);

        return Helper::retornoMobile($response);
    }

}