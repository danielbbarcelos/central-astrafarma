<?php 

namespace App\Http\Locators\Central; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller;
use App\Http\Controllers\Central\ProdutoController;

/**
 * @description Produto
 * Class ProdutoLocator
 * @package App\Http\Locators\Central
 */
class ProdutoLocator extends Controller
{

    //path's name of resources/views
    protected $basePathViews = 'pages.produtos.';


    //construct
    public function __construct()
    {
        $this->middleware('permissions', [ 'except' => []]);
    }


    /**
     * @description Lista de produtos
     * @info Listagem de produtos cadastrados via CentralVEX
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lista()
    {
        $controller = new ProdutoController();

        $response   = $controller->lista();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'lista', $response);
    }

    /**
     * @description Visualização de produto
     * @info Tela para visualizar dados de produtos via CentraVEX,
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function visualiza($produto_id)
    {
        $controller = new ProdutoController();

        $response   = $controller->visualiza($produto_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'produto', $response);
    }
}