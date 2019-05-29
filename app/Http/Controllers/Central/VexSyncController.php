<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
use App\Assinatura;
use App\EmpresaFilial;
use App\Perfil;
use App\User;

//mails

//framework
use App\Http\Controllers\Controller;
use App\UserDashboard;
use App\UserEmpresaFilial;
use App\Vendedor;
use App\VexSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//packages

//extras
use Validator; 

class VexSyncController extends Controller
{

    //construct
    public function __construct()
    {
         //
    }


    //retorna array do objeto
    public function lista($situacao)
    {
        $success = true;
        $log     = [];

        if($situacao !== 'com-sucesso' and $situacao !== 'sem-sucesso' and $situacao !== 'pendentes')
        {
            $success = false;
            $log[]   = ['error' => 'Requisição inválida'];
        }
        else
        {
            $syncs = VexSync::where(function($query) use ($situacao){

                if($situacao == 'com-sucesso')
                {
                    $query->where('status','1');
                    $query->where('sucesso','1');
                }
                elseif($situacao == 'sem-sucesso')
                {
                    $query->where('status','1');
                    $query->where('sucesso','0');
                }
                elseif($situacao == 'pendentes')
                {
                    $query->where('status','0');
                    $query->where('sucesso','0');
                }

            })->orderBy('updated_at','desc')->get();
        }

        //dd($syncs);

        $response['success'] = $success;
        $response['log']     = $log;
        $response['syncs']   = isset($syncs) ? $syncs : [];
        return $response;
    }


}