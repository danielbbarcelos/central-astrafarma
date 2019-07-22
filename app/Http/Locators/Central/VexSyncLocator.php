<?php 

namespace App\Http\Locators\Central; 

use App\Http\Controllers\Central\VexSyncController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 

/**
 * @description VEX Sync
 * Class VexSyncLocator
 * @package App\Http\Locators\Central
 */
class VexSyncLocator extends Controller
{

    //path's name of resources/views
    protected $basePathViews = 'pages.vex-sync.';


    //construct
    public function __construct()
    {
        $this->middleware('permissions', [ 'except' => []]);
    }


    /**
     * @description Lista de logs de sincronização
     * @info Listagem de logs de sincronização via CentralVEX
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lista($situacao)
    {
        $controller = new VexSyncController();

        $response   = $controller->lista($situacao);

        if(!$response['success'])
        {
            return Redirect::back()->withInput()->with('log',$response['log']);
        }

        return view($this->basePathViews.'lista', $response);
    }

    /**
     * @description Alteração de status do VEX Sync
     * @info Bloqueio e desbloqueio de item sincronizado no VEX Sync via CentralVEX
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function alteraStatus($id)
    {
        $controller = new VexSyncController();

        $response   = $controller->alteraStatus($id);

        return Redirect::back()->withInput()->with('log',$response['log']);

    }

}