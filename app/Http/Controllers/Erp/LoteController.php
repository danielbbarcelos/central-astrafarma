<?php 

namespace App\Http\Controllers\Erp; 

//models and controllers
use App\Armazem;
use App\EmpresaFilial;
use App\Lote;
use App\Produto;

//mails

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

//packages

//extras
use Validator; 
use Carbon\Carbon;

class LoteController extends Controller
{

    private static $logMessage = "Execução de VEX Sync em Erp\LoteController\n\n";


    //construct
    public function __construct()
    {
        //
    }



    //model update
    public static function update($array)
    {
        $success = true;
        $log     = self::$logMessage . json_encode($array)."\n\n";

        try 
        {
            foreach($array as $vars)
            {
                //busca dados da filial caso tenha sido enviada
                $empresaId = isset($vars['empresa_id']) ? $vars['empresa_id'] : null;
                $filialId  = isset($vars['filial_id'])  ? $vars['filial_id']  : null;

                unset($vars['empresa_id']);
                unset($vars['filial_id']);

                if($empresaId !== null and $filialId !== null)
                {
                    $empfil = EmpresaFilial::where('filial_erp_id',$filialId)->first();

                    if(isset($empfil))
                    {
                        $vars['vxgloempfil_id'] = $empfil->id;
                    }
                }

                //inclui timestamps
                $vars['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

                //formata datas
                $vars['dt_fabric'] = ($vars['dt_fabric'] !== '' and $vars['dt_fabric'] !== '0000-00-00') ? $vars['dt_fabric'] : null;
                $vars['dt_valid']  = ($vars['dt_valid'] !== '' and $vars['dt_valid'] !== '0000-00-00') ? $vars['dt_valid'] : null;

                //formata chaves
                $vars['vxestarmz_id'] = Armazem::where('erp_id',$vars['vxestarmz_erp_id'])->first()->id;
                $vars['vxgloprod_id'] = Produto::where('erp_id',$vars['vxgloprod_erp_id'])->first()->id;

                //formata ERP ID
                $vars['erp_id'] = str_replace($vars['erp_id'],'_','/');

                $lote = Lote::where('vxgloempfil_id', isset($vars['vxgloempfil_id']) ? $vars['vxgloempfil_id'] : null)
                    ->where('erp_id',$vars['erp_id'])
                    ->first();

                if(!isset($lote))
                {
                    $vars['created_at'] = Carbon::now()->format('Y-m-d H:i:s');

                    Lote::insert($vars);
                }
                else
                {
                    Lote::where('vxgloempfil_id', isset($vars['vxgloempfil_id']) ? $vars['vxgloempfil_id'] : null)
                        ->where('erp_id',$vars['erp_id'])
                        ->where('vxgloprod_id',$vars['vxgloprod_id'])
                        ->update($vars);
                }


                //verifica se há quantidade a ser empenhada
                \App\Http\Controllers\Central\LoteController::atualizaQuantidadeEmpenhada($vars['erp_id']);


            }

            $log .= "Procedimento realizado com sucesso";

        }
        catch(\Exception $e)
        {
            $success = false;
            $log     .= "Ocorreu um erro ao realizar o procedimento.\n\n";
            $log     .= 'Code '.$e->getFile().' - File: '.$e->getFile().' ('.$e->getLine().') - Message: '.$e->getMessage()."\n\n";
        }
        
        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }

}