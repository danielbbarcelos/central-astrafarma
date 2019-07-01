<?php 

namespace App\Http\Locators\Central; 

use App\Http\Controllers\Central\MigracaoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 

/**
 * @description Migração de base de dados
 * Class MigracaoLocator
 * @package App\Http\Locators\Central
 */
class MigracaoLocator extends Controller
{

    //path's name of resources/views
    protected $basePathViews = 'pages.migracao.';


    //construct
    public function __construct()
    {
        //
    }


    /**
     * @description Migração de dados via CentralVEX
     * @info Migração de dados via CentralVEX
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $controller = new MigracaoController();

        $response   = $controller->index();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'index', $response);
    }


    /**
     * @description Migração de dados via CentralVEX
     * @info Migração de dados via CentralVEX
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function migracaoPost(Request $request)
    {
        $controller = new MigracaoController();

        $response   = $controller->migracaoPost($request);

        return Redirect::back()->withInput()->with('log',$response['log']);

    }



}