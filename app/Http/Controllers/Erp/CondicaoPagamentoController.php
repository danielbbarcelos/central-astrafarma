<?php 

namespace App\Http\Controllers\Erp; 

//models and controllers
use App\CondicaoPagamento;

//mails

//framework
use App\EmpresaFilial;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

//packages

//extras
use Validator; 
use Carbon\Carbon;

class CondicaoPagamentoController extends Controller
{
    //construct
    public function __construct()
    {
        //
    }


    //model create
    public static function create($vars)
    {
        $success = true;
        $log     = '';

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


            //status automatico
            $vars['status'] = '1';

            $condicao = new CondicaoPagamento();

            $condicao->insert($vars);
        }
        catch(\Exception $e)
        {
            $success = false;
            $log     = 'Ocorreu um erro ao processar os itens';
        }
        
        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //model update
    public static function update($vars)
    {
        $success = true;
        $log     = '';

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
            
            //status automatico
            $vars['status'] = '1';

            CondicaoPagamento::where('vxgloempfil_id', isset($vars['vxgloempfil_id']) ? $vars['vxgloempfil_id'] : null)
                ->where('erp_id',$vars['erp_id'])
                ->update($vars);
        }
        catch(\Exception $e)
        {
            $success = false;
            $log     = 'Ocorreu um erro ao processar os itens';
        }
        
        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //model delete
    public static function delete($vars, EmpresaFilial $empfil = null)
    {
        $success = true;
        $log     = '';

        try 
        {
            $condicao = CondicaoPagamento::where('vxgloempfil_id', isset($empfil) ? $empfil->id : null)
                ->where('erp_id',$vars['erp_id'])
                ->first();

            $condicao->delete();
        }
        catch(\Exception $e)
        {
            $success = false;
            $log     = 'Ocorreu um erro ao processar os itens';
        }
        
        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }
}