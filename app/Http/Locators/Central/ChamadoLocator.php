<?php 

namespace App\Http\Locators\Central; 

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Central\ChamadoController;

/**
 * @description Chamados
 * Class ChamadoLocator
 * @package App\Http\Locators\Central
 */
class ChamadoLocator extends Controller
{

    //path's name of resources/views
    protected $basePathViews = 'pages.chamados.';


    //construct
    public function __construct()
    {
        $this->middleware('permissions', [ 'except' => []]);
    }


    /**
     * @description Lista de chamados
     * @info Listagem de chamados abertos via CentralVEX
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lista(Request $request)
    {
        $controller = new ChamadoController();

        $response   = $controller->lista();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'lista', $response);
    }


    /**
     * @description Abertura de chamados
     * @info Abertura de chamados de suporte via CentralVEX
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function adicionaPost(Request $request)
    {
        $controller = new ChamadoController();

        $response   = $controller->adicionaPost($request);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/suporte/chamados/'.$response['chamado']->id.'/show')->with('log',$response['log']);
    }


    /**
     * @description Visualização de chamado
     * @info Tela de visualização do chamado e interações via CentralVEX
     * @param $chamado_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function visualiza($chamado_id)
    {
        $controller = new ChamadoController();

        $response   = $controller->visualiza($chamado_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'chamado', $response);
    }


    /**
     * @description Interação de chamado
     * @info Realizar interações em chamados via CentralVEX
     * @param Request $request
     * @param $chamado_id
     * @return mixed
     */
    public function interagePost(Request $request, $chamado_id)
    {
        $controller = new ChamadoController();

        $response   = $controller->interagePost($request, $chamado_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return Redirect::back()->withInput()->with('log',$response['log']);
    }

}