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

                    $registro = "Iniciando VEX Sync do registro (ID) ".$object->id."\n\n";


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

                            $registro .= "Sincronizacao realizada com sucesso \n\n";
                        }
                        else 
                        {
                            $syncLog = ['data_hora' => Carbon::now()->format('Y-m-d H:i:s'), 'sucesso' => '0', 'mensagem' => $return['log']];

                            $registro .= "Erro ao sincronizar na Central VEX: {$return['log']} \n\n";
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


                            $registro .= "VEX Sync atualizado com sucesso no ERP\n\n";

                        }
                        catch(\Exception $e2)
                        {
                            $success = false;
                            $log     = $e2->getMessage();

                            $registro .= "\nERRO: Linha: {$e2->getLine()}\nArquivo: {$e2->getFile()}\nCódigo: {$e2->getCode()}\nMensagem {$e2->getMessage()}";
                        }


                        Helper::logFile('vex-sync-erp.log', $registro);

                    }
                }
            }
        }
        catch(\Exception $e)
        {
            Log::info($e->getMessage());
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
        $log     = '';

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
            $controller::create($result);
        }
        catch(\Exception $e)
        {
            $success = false;
            $log     = $e->getMessage();
        }

        $response['success']    = $success;
        $response['log']        = $log;
        return $response;
    }


    public static function update($object)
    {
        $success = true;
        $log     = '';

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
            $controller::update($result);

        }
        catch(\Exception $e)
        {
            $success = false;
            $log     = $e->getMessage();
        }

        $response['success']    = $success;
        $response['log']        = $log;
        return $response;
    }

    
    public static function delete($object)
    {
        $success = true;
        $log     = '';

        try
        {
            //busca a controller para realizar o insert
            $controller = Aliases::erpControllerByTable($object->tabela);

            //executa o processamento no banco de dados
            $result['erp_id'] = $object->erp_id;

            $controller::delete($result, Helper::converteTenantId($object->tenant));
        }
        catch(\Exception $e)
        {
            $success = false;
            $log     = $e->getMessage();
        }

        $response['success']    = $success;
        $response['log']        = $log;
        return $response;
    }

}
