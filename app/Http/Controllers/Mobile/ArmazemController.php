<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Armazem;

//mails

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator; 
use Carbon\Carbon;


class ArmazemController extends Controller
{

    //construct
    public function __construct()
    {
         //
    }


    public function lista()
    {
        $success = true;
        $log     = [];

        $armazens = WebService::armazens();

        $response['success']  = $success;
        $response['log']      = $log;
        $response['armazens'] = $armazens;
        return $response;
    }



    public function visualiza($armazem_id)
    {
        $success = true;
        $log     = [];

        $armazem = WebService::armazens();

        $response['success'] = $success;
        $response['log']     = $log;
        $response['armazem'] = $armazem[0];
        return $response;
    }



}