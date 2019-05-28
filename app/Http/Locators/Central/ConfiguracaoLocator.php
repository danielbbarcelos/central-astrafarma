<?php

namespace App\Http\Locators\Central;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Central\ConfiguracaoController;

/**
 * @description Configurações gerais
 * Class ConfiguracaoLocator
 * @package App\Http\Locators\Central
 */
class ConfiguracaoLocator extends Controller
{

    //path's name of resources/views
    protected $basePathViews = 'pages.configuracoes.';


    //construct
    public function __construct()
    {
        $this->middleware('permissions', [ 'except' => []]);
    }

    /**
     * @description Tela de configurações gerais
     * @info Tela de configuração geral na CentralVEX
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function visualiza()
    {
        $controller = new ConfiguracaoController();

        $response   = $controller->visualiza();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'configuracao', $response);
    }

    /**
     * @description Configurações gerais
     * @info Configuração geral na CentralVEX
     * @param Request $request
     * @return mixed
     */
    public function editaPost(Request $request)
    {
        $controller = new ConfiguracaoController();

        $response   = $controller->editaPost($request);

        return Redirect::back()->withInput()->with('log',$response['log']);

    }

}