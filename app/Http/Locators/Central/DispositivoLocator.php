<?php 

namespace App\Http\Locators\Central; 

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Central\DispositivoController;

/**
 * @description Dispositivos
 * Class DispositivoLocator
 * @package App\Http\Locators\Central
 */
class DispositivoLocator extends Controller
{

    //path's name of resources/views
    protected $basePathViews = 'pages.dispositivos.';


    //construct
    public function __construct()
    {
        $this->middleware('permissions', [ 'except' => []]);
    }


    /**
     * @description Lista de dispositivos
     * @info Listagem de dispositivos cadastrados via CentralVEX
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lista(Request $request)
    {
        $controller = new DispositivoController();

        $response   = $controller->lista();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'lista', $response);
    }


    /**
     * @description Tela de cadastro de dispositivos
     * @info Tela para cadastro de dispositivos via CentraVEX, para utilização da API
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adiciona(Request $request)
    {
        $controller = new DispositivoController();

        $response   = $controller->adiciona();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        $response['action'] = 'create';

        return view($this->basePathViews.'dispositivo', $response);
    }


    /**
     * @description Cadastro de dispositivos
     * @info Cadastro de dispositivos via CentraVEX, para utilização da API
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function adicionaPost(Request $request)
    {
        $controller = new DispositivoController();

        $response   = $controller->adicionaPost($request);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/dispositivos')->with('log',$response['log']);
    }


    /**
     * @description Tela de edição de dispositivo
     * @info Tela para editar dados de dispositivos via CentraVEX, para utilização da API
     * @param Request $request
     * @param $dispositivo_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edita(Request $request, $dispositivo_id)
    {
        $controller = new DispositivoController();

        $response   = $controller->edita($dispositivo_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        $response['action'] = 'update';

        return view($this->basePathViews.'dispositivo', $response);
    }


    /**
     * @description Edição de dispositivo
     * @info Edição de dados de dispositivos via CentraVEX, para utilização da API
     * @param Request $request
     * @param $dispositivo_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editaPost(Request $request, $dispositivo_id)
    {
        $controller = new DispositivoController();

        $response   = $controller->editaPost($request, $dispositivo_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/dispositivos')->with('log',$response['log']);
    }


    /**
     * @description Tela de visualização de dispositivo
     * @info Tela para visualizar dados de dispositivos via CentraVEX, para utilização da API
     * @param $dispositivo_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function visualiza($dispositivo_id)
    {
        $controller = new DispositivoController();

        $response   = $controller->visualiza($dispositivo_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        $response['action'] = 'read';

        return view($this->basePathViews.'dispositivo', $response);
    }


    /**
     * @description Exclusão de dispositivos
     * @info Exclusão de cadastro de dispositivos via CentralVEX
     * @param Request $request
     * @param $dispositivo_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function excluiPost(Request $request, $dispositivo_id)
    {
        $controller = new DispositivoController();

        $response   = $controller->excluiPost($request, $dispositivo_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/dispositivos')->with('log',$response['log']);
    }


}