<?php 

namespace App\Http\Controllers\Erp; 

//models and controllers
use App\Assinatura;
use App\VexSync;

//mails

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

//packages
use GuzzleHttp\Client;

//extras
use Illuminate\Support\Facades\Log;
use Validator;
use Carbon\Carbon;
use App\Utils\Aliases;
use App\Utils\Helper;

class VexSyncController extends Controller
{

    private static $logMessage = 'VEX Sync via ERP - ID ';

    //construct
    public function __construct()
    {
         //
    }


    //busca pendencias no ERP
    public static function buscaPendencia()
    {
        $success = true;
        $log     = [];

        $assinatura = Assinatura::first();

        $vexSync = new VexSync();

        Log::info('Buscando pendências na CentralVEX');

        try 
        {
            //busca o cliente no protheus
            $guzzle  = new Client();
            $result  = $guzzle->request('GET', $assinatura->webservice_base . $vexSync->getWebservice());
            $result  = json_decode($result->getBody());


            if($result->success == false)
            {
                $success = false;
                $log[]   = ['error' => $result->log];
            }
            else 
            {
                $objects = Helper::retornoERP($result->result);
                $objects = json_decode($objects);

                foreach($objects as $object)
                {

                    if(strtolower($object->action) == 'create')
                    {
                        $return = VexSyncController::create($object);
                    }
                    elseif(strtolower($object->action) == 'update')
                    {
                        $return = VexSyncController::update($object);
                    }
                    elseif(strtolower($object->action) == 'delete')
                    {
                        $return = VexSyncController::delete($object);
                    }

                    
                    if(isset($return))
                    {
                        //trata o retorno da ação
                        if($return['success'] == true)
                        {
                            $syncLog = ['data_hora' => Carbon::now()->format('Y-m-d H:i:s'), 'sucesso' => '1', 'mensagem' => 'Sincronizacao realizada com sucesso'];
                        }
                        else 
                        {
                            $syncLog = ['data_hora' => Carbon::now()->format('Y-m-d H:i:s'), 'sucesso' => '0', 'mensagem' => $return['log']];
                        }

                        try 
                        {
                            $guzzle  = new Client();
                            $result  = $guzzle->request('PUT', $assinatura->webservice_base . $vexSync->getWebservice(), [
                                'headers'     => [
                                    'Content-Type' => 'application/json',
                                ], 
                                'body' => json_encode([
                                    'id'      => (int)$object->id,
                                    'status'  => '1',
                                    'sucesso' => $return['success'] == true ? '1' : '0',
                                    'logsync' => json_encode($syncLog),
                                ])
                            ]);
                        }
                        catch(\Exception $e2)
                        {
                            $success = false;
                            $log     = $e2->getMessage();
                        }


                        //salva log de sincronização
                        Helper::logFile('vex-sync-erp-'.Carbon::now()->format('Y-m-d').'.log', $return['log']);
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $success = false;
            $log[]   = ['error' => $e->getMessage()];

        }
    
        $response['success']    = $success;
        $response['log']        = $log;
        return $response;
    }


    public static function create($object)
    {
        $success = true;
        $log     = self::$logMessage." $object->id. \n\n";

        $assinatura = Assinatura::first();

        try
        {
            $guzzle  = new Client();
            $result  = $guzzle->request('GET', $assinatura->webservice_base . $object->ws);
            $result  = json_decode($result->getBody());

            $result = Helper::retornoERP($result->result);
            $result = json_decode($result, true);

            //busca a controller para realizar o insert
            $controller = Aliases::erpControllerByTable($object->tabela);

            //executa o processamento no banco de dados
            $log    .= $controller::create($result);
        }
        catch(\Exception $e)
        {
            $success = false;
            $log    .= $e->getMessage();
        }

        $response['success']    = $success;
        $response['log']        = $log;
        return $response;
    }


    public static function update($object)
    {
        $success = true;
        $log     = self::$logMessage." $object->id. \n\n";

        $assinatura = Assinatura::first();

        try
        {
            $guzzle  = new Client();
            $result  = $guzzle->request('GET', $assinatura->webservice_base . $object->ws);
            $result  = json_decode($result->getBody());


            $result = Helper::retornoERP($result->result);
            $result = json_decode($result, true);


            //busca a controller para realizar o insert
            $controller = Aliases::erpControllerByTable($object->tabela);

            //executa o processamento no banco de dados
            $log    .= $controller::update($result);

        }
        catch(\Exception $e)
        {
            $success = false;
            $log    .= $e->getMessage();
        }

        $response['success']    = $success;
        $response['log']        = $log;
        return $response;
    }

    
    public static function delete($object)
    {
        $success = true;
        $log     = self::$logMessage." $object->id. \n\n";

        try
        {
            //busca a controller para realizar o insert
            $controller = Aliases::erpControllerByTable($object->tabela);

            //executa o processamento no banco de dados
            $result['erp_id'] = $object->erp_id;

            $log    .= $controller::delete($result, Helper::converteTenantId($object->tenant));
        }
        catch(\Exception $e)
        {
            $success = false;
            $log    .= $e->getMessage();
        }

        $response['success']    = $success;
        $response['log']        = $log;
        return $response;
    }

}
