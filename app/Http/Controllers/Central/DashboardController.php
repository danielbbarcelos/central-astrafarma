<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
use App\Assinatura;

//mails

//framework
use App\EmpresaFilial;
use App\Http\Controllers\Controller;
use App\UserDashboard;
use App\UserEmpresaFilial;
use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

//packages

//extras
use Validator;


class DashboardController extends Controller
{
    protected $empfilId;

    //construct
    public function __construct()
    {
        $this->empfilId = Auth::user()->userEmpresaFilial->empfil->id;
    }


    //chamada da tela do dashboard
    public function dashboard()
    {
        $success = true;
        $log     = [];

        //dashboard
        $dashboard = UserDashboard::where('vxwebuser_id',Auth::user()->id)->first();

        if(!isset($dashboard))
        {
            $dashboard = new UserDashboard();
            $dashboard->vxwebuser_id   = Auth::user()->id;
            $dashboard->assinatura_status = '0';
            $dashboard->bi_status         = '0';
            $dashboard->bi_url            = null;
            $dashboard->created_at        = new \DateTime();
            $dashboard->updated_at        = new \DateTime();
            $dashboard->save();
        }

        //Busca dados da assinatura da empresa
        $assinatura = Assinatura::first();

        //busca as filiais cadastradas
        $filiais = UserEmpresaFilial::where('vxwebuser_id',Auth::user()->id)->get();

        $response['success']    = $success;
        $response['log']        = $log;
        $response['dashboard']  = $dashboard;
        $response['assinatura'] = $assinatura;
        $response['filiais']    = $filiais;
        return $response;
    }


    //chamada para selecionar a filial
    public function selecionaFilial($empfil_id)
    {
        $success = true;
        $log     = [];

        $empfil = EmpresaFilial::find($empfil_id);

        if(!isset($empfil))
        {
            $success = false;
            $log[]   = ['error' => 'Filial não encontrada'];
        }
        else
        {
            $user = Auth::user();
            $user->vxgloempfil_id = $empfil_id;
            $user->updated_at     = new \DateTime();
            $user->save();

            $log[] = ['success' => 'Ambiente alterado para filial '.$empfil->nome.' - CNPJ: '.Helper::insereMascara($empfil->cnpj,'##.###.###/####-##')];
        }

        $response['success']    = $success;
        $response['log']        = $log;
        return $response;
    }
}