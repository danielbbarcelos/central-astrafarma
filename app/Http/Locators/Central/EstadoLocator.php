<?php 

namespace App\Http\Locators\Central;

use App\Http\Controllers\Central\EstadoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect; 
use App\Http\Controllers\Controller; 

class EstadoLocator extends Controller
{
    //construct
    public function __construct()
    {
        $this->middleware('permissions', [ 'except' => ['buscaCidades']]);
    }


    //retorna cidades através do estado selecionado
    public function buscaCidades($uf)
    {
        $controller = new EstadoController();

        $response   = $controller->buscaCidades($uf);

        return $response;
    }
}