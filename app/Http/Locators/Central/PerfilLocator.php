<?php 

namespace App\Http\Locators\Central; 

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Central\PerfilController;

/**
 * @description Perfis de acesso
 * Class PerfilLocator
 * @package App\Http\Locators\Central
 */
class PerfilLocator extends Controller
{

    //path's name of resources/views
    protected $basePathViews = 'pages.perfis.';


    //construct
    public function __construct()
    {
        $this->middleware('permissions', [ 'except' => []]);
    }


    /**
     * @description Lista de perfis de acesso
     * @info Listagem de perfis de acesso cadastrados via CentralVEX
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lista(Request $request)
    {
        $controller = new PerfilController();

        $response   = $controller->lista();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'lista', $response);
    }


    /**
     * @description Tela de cadastro de perfis de acesso
     * @info Tela para cadastro de perfis de acesso via CentraVEX
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adiciona(Request $request)
    {
        $controller = new PerfilController();

        $response   = $controller->adiciona();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        $response['action'] = 'create';

        return view($this->basePathViews.'perfil', $response);
    }


    /**
     * @description Cadastro de perfis de acesso
     * @info Cadastro de perfis de acesso via CentraVEX
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function adicionaPost(Request $request)
    {
        $controller = new PerfilController();

        $response   = $controller->adicionaPost($request);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/perfis')->with('log',$response['log']);
    }


    /**
     * @description Tela de edição de perfil de acesso
     * @info Tela para editar dados de perfis de acesso via CentraVEX
     * @param Request $request
     * @param $perfil_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edita(Request $request, $perfil_id)
    {
        $controller = new PerfilController();

        $response   = $controller->edita($perfil_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        $response['action'] = 'update';

        return view($this->basePathViews.'perfil', $response);
    }


    /**
     * @description Edição de perfil de acesso
     * @info Edição de dados de perfis de acesso via CentraVEX
     * @param Request $request
     * @param $perfil_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editaPost(Request $request, $perfil_id)
    {
        $controller = new PerfilController();

        $response   = $controller->editaPost($request, $perfil_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/perfis')->with('log',$response['log']);
    }


    /**
     * @description Tela de visualização de perfil de acesso
     * @info Tela para visualizar dados de perfis de acesso via CentraVEX
     * @param $perfil_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function visualiza(Request $request, $perfil_id)
    {
        $controller = new PerfilController();

        $response   = $controller->visualiza($perfil_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        $response['action'] = 'read';

        return view($this->basePathViews.'perfil', $response);
    }


    /**
     * @description Exclusão de perfis de acesso
     * @info Exclusão de cadastro de perfis de acesso via CentralVEX
     * @param Request $request
     * @param $perfil_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function excluiPost(Request $request, $perfil_id)
    {
        $controller = new PerfilController();

        $response   = $controller->excluiPost($request, $perfil_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/perfis')->with('log',$response['log']);
    }


}