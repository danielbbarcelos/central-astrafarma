<?php 

namespace App\Http\Locators\Central; 

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Central\UserController;

/**
 * @description Usuários
 * Class UserLocator
 * @package App\Http\Locators\Central
 */
class UserLocator extends Controller
{

    //path's name of resources/views
    protected $basePathViews = 'pages.usuarios.';


    //construct
    public function __construct()
    {
        $this->middleware('permissions', [ 'except' => []]);
    }


    /**
     * @description Lista de usuários
     * @info Listagem de usuários cadastrados via CentralVEX
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lista(Request $request)
    {
        $controller = new UserController();

        $response   = $controller->lista();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'lista', $response);
    }


    /**
     * @description Tela de cadastro de usuários
     * @info Tela para cadastro de usuários via CentraVEX, para utilização da API
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adiciona(Request $request)
    {
        $controller = new UserController();

        $response   = $controller->adiciona();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        $response['action'] = 'create';

        return view($this->basePathViews.'usuario', $response);
    }


    /**
     * @description Cadastro de usuários
     * @info Cadastro de usuários via CentraVEX, para utilização da API
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function adicionaPost(Request $request)
    {
        $controller = new UserController();

        $response   = $controller->adicionaPost($request);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/usuarios')->with('log',$response['log']);
    }


    /**
     * @description Tela de edição de usuário
     * @info Tela para editar dados de usuários via CentraVEX, para utilização da API
     * @param Request $request
     * @param $user_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edita(Request $request, $user_id)
    {
        $controller = new UserController();

        $response   = $controller->edita($user_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        $response['action'] = 'update';

        return view($this->basePathViews.'usuario', $response);
    }


    /**
     * @description Edição de usuário
     * @info Edição de dados de usuários via CentraVEX, para utilização da API
     * @param Request $request
     * @param $user_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editaPost(Request $request, $user_id)
    {
        $controller = new UserController();

        $response   = $controller->editaPost($request, $user_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/usuarios')->with('log',$response['log']);
    }


    /**
     * @description Tela de visualização de usuário
     * @info Tela para visualizar dados de usuários via CentraVEX, para utilização da API
     * @param $user_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function visualiza(Request $request, $user_id)
    {
        $controller = new UserController();

        $response   = $controller->visualiza($user_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        $response['action'] = 'read';

        return view($this->basePathViews.'usuario', $response);
    }


    /**
     * @description Exclusão de usuários
     * @info Exclusão de cadastro de usuários via CentralVEX
     * @param Request $request
     * @param $user_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function excluiPost(Request $request, $user_id)
    {
        $controller = new UserController();

        $response   = $controller->excluiPost($request, $user_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/usuarios')->with('log',$response['log']);
    }


}