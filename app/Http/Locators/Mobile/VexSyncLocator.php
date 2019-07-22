<?php 

namespace App\Http\Locators\Mobile; 

use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Mobile\VexSyncController as MobileVexSyncController;
use App\Http\Controllers\Erp\VexSyncController as ErpVexSyncController;

/**
 * @description VEX Sync
 * Class VexSyncLocator
 * @package App\Http\Locators\Mobile
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
     * @info Sincronizar dados da plataforma via API
     * @param Request $request
     * @return mixed
     */
    public function sincroniza(Request $request)
    {
        $controller = new MobileVexSyncController();

        $controller::sincroniza();

        $controller = new ErpVexSyncController();

        $controller::buscaPendencia();

        if(isset($request['web']))
        {
            $log   = [];
            $log[] = ['success'=>'Sincronização realizada'];

            return Redirect::back()->with('log',$log);
        }
        else
        {
            $response['success'] = true;
            $response['log']     = [];
            return Helper::retornoMobile($response);
        }
    }
}