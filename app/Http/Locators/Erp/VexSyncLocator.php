<?php 

namespace App\Http\Locators\Erp;

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Erp\VexSyncController;

/**
 * @description VEX Sync
 * Class VexSyncLocator
 * @package App\Http\Locators\Erp
 */
class VexSyncLocator extends Controller
{
    //construct
    public function __construct()
    {
        //
    }

    /**
     * @description Sincronização VEX
     * @info Sincronizar dados da plataforma via CentralVEX
     * @param Request $request
     * @return mixed
     */
    public function buscaPendencia(Request $request)
    {
        $controller = new VexSyncController();

        $response   = $controller::buscaPendencia();

        return $response;
    }
}