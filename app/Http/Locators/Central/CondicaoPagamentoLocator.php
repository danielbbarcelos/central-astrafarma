<?php 

namespace App\Http\Locators\Central; 

use App\Http\Controllers\Central\CondicaoPagamentoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 

/**
 * @description Condições de pagamento
 * Class CondicaoPagamentoLocator
 * @package App\Http\Locators\Central
 */
class CondicaoPagamentoLocator extends Controller
{

    //path's name of resources/views
    protected $basePathViews = 'pages.condicoes-pagamentos.';


    //construct
    public function __construct()
    {
        $this->middleware('permissions', [ 'except' => []]);
    }


    /**
     * @description Lista de condições de pagamento
     * @info Listagem de condições de pagamento cadastrados via CentralVEX
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lista(Request $request)
    {
        $controller = new CondicaoPagamentoController();

        $response   = $controller->lista();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'lista', $response);
    }


}