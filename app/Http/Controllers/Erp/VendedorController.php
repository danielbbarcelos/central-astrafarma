<?php 

namespace App\Http\Controllers\Erp; 

//models and controllers
use App\EmpresaFilial;
use App\Vendedor;

//mails

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

//packages

//extras
use Validator; 
use Carbon\Carbon;

class VendedorController extends Controller
{

    private static $logMessage = "Execução de VEX Sync em Erp\VendedorController\n\n";

    //construct
    public function __construct()
    {
        //
    }
    
    //model create
    public static function create($vars)
    {
        $success = true;
        $log     = self::$logMessage . json_encode($vars)."\n\n";

        try 
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
            $vars['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $vars['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

            $vendedor = new Vendedor();

            $vendedor->insert($vars);

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


    //model update
    public static function update($vars)
    {
        $success = true;
        $log     = self::$logMessage . json_encode($vars)."\n\n";

        try 
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

            Vendedor::where('vxgloempfil_id', isset($vars['vxgloempfil_id']) ? $vars['vxgloempfil_id'] : null)
                ->where('erp_id',$vars['erp_id'])
                ->update($vars);

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


    //model delete
    public static function delete($vars, EmpresaFilial $empfil = null)
    {
        $success = true;
        $log     = self::$logMessage . json_encode($vars)."\n\n";

        try 
        {
            Vendedor::where('vxgloempfil_id', isset($empfil) ? $empfil->id : null)
                ->where('erp_id',$vars['erp_id'])
                ->delete();

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