<?php 

namespace App\Http\Controllers\Mobile; 


//models and controllers
use App\Assinatura;
use App\VexSync;

//mails

//framework
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator; 
use Carbon\Carbon;
use App\Utils\Helper;
use App\Utils\Aliases;

class VexSyncController extends Controller
{

    //construct
    public function __construct()
    {
         //
    }


    //adiciona registro no vex sync
    public static function adiciona($tenantId, $action, $tabela, $id, $webservice)
    {
        $success = true;
        $log     = [];

        //gera registro de sincronização
        $sync = new VexSync();
        $sync->tenant     = $tenantId;
        $sync->action     = $action;
        $sync->tabela     = $tabela;
        $sync->tabela_id  = $id;
        $sync->webservice = $webservice;
        $sync->status     = '0';
        $sync->sucesso    = '0';
        $sync->log        = null;
        $sync->created_at = new \DateTime();
        $sync->updated_at = new \DateTime();
        $sync->save();

        $response['success']  = $success;
        $response['log']      = $log;
        return $response;
    }


    //busca sincronizações pendentes
    public static function sincroniza()
    {
        $success = true;
        $log     = [];

        $syncs = VexSync::where('status','0')->get();

        foreach($syncs as $sync)
        {
            //informa que já foi executado
            $sync->status = '1';
            $sync->save();


            //verifica qual ação deverá ser executada
            if($sync->action == 'post')
            {
                $action = VexSyncController::post($sync);
            }
            else if($sync->action == 'put')
            {
                $action = VexSyncController::put($sync);
            }


            //trata o retorno da ação
            if($action['success'])
            {
                $syncLog = ['data_hora' => Carbon::now()->format('Y-m-d H:i:s'), 'sucesso' => true, 'mensagem' => 'Sincronização realizada com sucesso'];
            }
            else 
            {
                $syncLog = ['data_hora' => Carbon::now()->format('Y-m-d H:i:s'), 'sucesso' => false, 'mensagem' => $action['log']];
            }

            $sync->sucesso      = $action['success'] == true ? '1' : '0';
            $sync->log          = json_encode($syncLog);
            $sync->updated_at   = new \DateTime();
            $sync->save();
        }

        $response['success']  = $success;
        $response['log']      = $log;
        return $response;
    }


    //executa post
    public static function post(VexSync $sync)
    {
        $success = true;
        $log     = '';

        //busca a controller para realizar o insert
        $controller = Aliases::mobileControllerByTable($sync->tabela);

        //executa o processamento no banco de dados
        $response = $controller::syncPost($sync);
        
        return $response;
    }


    //executa put
    public static function put(VexSync $sync)
    {
        $success = true;
        $log     = '';

        //busca a controller para realizar o insert
        $controller = Aliases::mobileControllerByTable($sync->tabela);

        //executa o processamento no banco de dados
        $response = $controller::syncPut($sync);
        
        return $response;
    }

}
