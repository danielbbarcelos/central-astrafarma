<?php 

namespace App\Http\Locators\Central; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller;
use App\Http\Controllers\Central\DashboardController;

/**
 * @description Dashboard
 * Class DashboardLocator
 * @package App\Http\Locators\Central
 */
class DashboardLocator extends Controller
{

    //path's name of resources/views
    protected $basePathViews = 'pages.dashboard.';


    //construct
    public function __construct()
    {
        //
    }


    /**
     * @description Dashboard na tela de início
     * @info Dashboard na tela de início da CentralVEX
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard()
    {
        $controller = new DashboardController();

        $response   = $controller->dashboard();

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'dashboard', $response);
    }

    /**
     * @description Seleção de filial
     * @info Opção para selecionar filial, em que as opçerações são executadas na CentralVEX
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function selecionaFilial($empfil_id)
    {
        $controller = new DashboardController();

        $response   = $controller->selecionaFilial($empfil_id);

        return Redirect::back()->withInput()->with('log',$response['log']);

    }
}