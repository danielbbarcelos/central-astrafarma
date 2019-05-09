<?php 

namespace App\Http\Locators\Central; 

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller;
use App\Http\Controllers\Central\AuthController;

/**
 * @description Autenticação
 * Class AuthLocator
 * @package App\Http\Locators\Central
 */
class AuthLocator extends Controller
{

    //path's name of resources/views
    protected $basePathViews = 'pages.auth.';


    //construct
    public function __construct()
    {
        //
    }


    /**
     * @description Tela de login
     * @info Tela para realizar o login via CentralVEX
     * @param Request $request
     * @return mixed
     */
    public function login()
    {
        $controller = new AuthController();

        $response   = $controller->login();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'login', $response);
    }


    /**
     * @description Login via CentralVEX
     * @info Realização de login via CentralVEX
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginPost(Request $request)
    {
        $controller = new AuthController();

        $response   = $controller->loginPost($request);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/dashboard')->with('log',$response['log']);
    }


    /**
     * @description Logout
     * @info Realização de logout via CentralVEX
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        $controller = new AuthController();

        $response   = $controller->logout();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/');
    }


    /**
     * @description Tela de recuperação de senha
     * @info Tela para recuperar senha de acesso via CentralVEX
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function recuperaSenha()
    {
        $controller = new AuthController();

        $response   = $controller->recuperaSenha();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'recuperacao-senha', $response);
    }


    /**
     * @description Recuperação de senha
     * @info Recuperação de senha de acesso via CentralVEX
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function recuperaSenhaPost(Request $request)
    {
        $controller = new AuthController();

        $response   = $controller->recuperaSenhaPost($request);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('error', $response['log']);
        }

        return redirect('/')->with('success', $response['log']);
    }


    /**
     * @description Tela de alteração de senha
     * @info Tela para alterar senha de acesso via CentralVEX
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function alteraSenha()
    {
        $controller = new AuthController();

        $response   = $controller->alteraSenha();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'password', $response);
    }


    /**
     * @description Alteração de senha
     * @info Alteração de senha de acesso via CentralVEX
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function alteraSenhaPost(Request $request)
    {
        $controller = new AuthController();

        $response   = $controller->alteraSenhaPost($request);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/dashboard')->with('log',$response['log']);
    }

}