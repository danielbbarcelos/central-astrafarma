<?php 

namespace App\Http\Locators\Mobile; 

use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 
use App\Http\Controllers\Mobile\AuthController;

/**
 * @description Autenticação
 * Class AuthLocator
 * @package App\Http\Locators\Mobile
 */
class AuthLocator extends Controller
{
    //construct
    public function __construct()
    {
        //
    }

    /**
     * @description Login via API
     * @info Realização de login via API, com validação de dispositivo cadastrado
     * @param Request $request
     * @return mixed
     */
    public function loginPost(Request $request)
    {
        $controller = new AuthController();

        $response   = $controller->loginPost($request);

        return Helper::retornoMobile($response);
    }

}