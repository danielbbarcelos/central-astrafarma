<?php 

namespace App\Http\Locators\Central; 

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Central\ClienteController;

/**
 * @description Clientes
 * Class ClienteLocator
 * @package App\Http\Locators\Central
 */
class ClienteLocator extends Controller
{

    //path's name of resources/views
    protected $basePathViews = 'pages.clientes.';


    //construct
    public function __construct()
    {
        $this->middleware('permissions', [ 'except' => []]);
        $this->middleware('uservend',    [ 'except' => []]);
    }


    /**
     * @description Lista de clientes
     * @info Listagem de clientes cadastrados via CentralVEX
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lista(Request $request)
    {
        $controller = new ClienteController();

        $response   = $controller->lista();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'lista', $response);
    }


    /**
     * @description Tela de cadastro de clientes
     * @info Tela para cadastro de clientes via CentraVEX, para utilização da API
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adiciona(Request $request)
    {
        $controller = new ClienteController();

        $response   = $controller->adiciona();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        $response['action'] = 'create';

        return view($this->basePathViews.'cliente', $response);
    }


    /**
     * @description Cadastro de clientes
     * @info Cadastro de clientes via CentraVEX, para utilização da API
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function adicionaPost(Request $request)
    {
        $controller = new ClienteController();

        $response   = $controller->adicionaPost($request);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/clientes')->with('log',$response['log']);
    }


    /**
     * @description Tela de edição de usuário
     * @info Tela para editar dados de clientes via CentraVEX, para utilização da API
     * @param Request $request
     * @param $cliente_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edita(Request $request, $cliente_id)
    {
        $controller = new ClienteController();

        $response   = $controller->edita($cliente_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        $response['action'] = 'update';

        return view($this->basePathViews.'cliente', $response);
    }


    /**
     * @description Edição de usuário
     * @info Edição de dados de clientes via CentraVEX, para utilização da API
     * @param Request $request
     * @param $cliente_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editaPost(Request $request, $cliente_id)
    {
        $controller = new ClienteController();

        $response   = $controller->editaPost($request, $cliente_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return \redirect('/clientes')->with('log',$response['log']);
    }


    /**
     * @description Tela de visualização de usuário
     * @info Tela para visualizar dados de clientes via CentraVEX, para utilização da API
     * @param $cliente_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function visualiza(Request $request, $cliente_id)
    {
        $controller = new ClienteController();

        $response   = $controller->visualiza($cliente_id);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        $response['action'] = 'read';

        return view($this->basePathViews.'cliente', $response);
    }

}