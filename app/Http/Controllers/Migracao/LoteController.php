<?php

namespace App\Http\Controllers\Migracao;

//models and controllers
use App\Armazem;
use App\Assinatura;
use App\Cliente;

//mails

//framework
use App\CondicaoPagamento;
use App\EmpresaFilial;
use App\Http\Controllers\Controller;
use App\Lote;
use App\Produto;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Illuminate\Support\Facades\Log;
use Validator;
use Carbon\Carbon;


class LoteController extends Controller
{
    //construct
    public function __construct()
    {
        //
    }


    //model create
    public static function migracao($uri = 'all')
    {
        $success = true;
        $log     = '';

        error_reporting(-1);//report all errors

        ini_set('display_errors', 1);//display errors to standard output

        $assinatura = Assinatura::first();
        $lote       = new Lote();
        $webservice = $assinatura->webservice_base . $lote->getWebservice() . $uri;
        
        $guzzle  = new Client();
        $result  = $guzzle->request('GET', $webservice);

        $result  = html_entity_decode(mb_convert_encoding(stripslashes($result->getBody()), "HTML-ENTITIES", 'UTF-8'));

        $result  = json_decode(  $result, true);

        if($result == null)
        {
            $success = false;
            $log     = 'Não foi possível importar os registros encontrados';
        }
        else
        {
            Lote::truncate();

            $error = 0;

            foreach($result['result'] as $item)
            {
                if($item['VXGLOPROD_ERP_ID'] !== '')
                {
                        $empfil = EmpresaFilial::where('filial_erp_id',$item['FILIAL_ID'])->first();

                        $armazem = Armazem::where('erp_id',isset($item['VXESTARMZ_ERP_ID']) ? $item['VXESTARMZ_ERP_ID'] : $item['ARMAZEM'])->first();

                        $produto = Produto::where('erp_id',$item['VXGLOPROD_ERP_ID'])->first();

                        $lote = new Lote();
                        $lote->erp_id             = $item['ERP_ID'];
                        $lote->vxgloempfil_id     = isset($empfil) ? $empfil->id : '1';
                        $lote->vxestarmz_id       = isset($armazem) ? $armazem->id : null;
                        $lote->vxestarmz_erp_id   = isset($armazem) ? $armazem->erp_id : null;
                        $lote->vxgloprod_id       = isset($produto) ? $produto->id : null;
                        $lote->vxgloprod_erp_id   = isset($produto) ? $produto->erp_id : null;
                        $lote->dt_fabric          = $item['DT_FABRIC'] !== '0000-00-00' ? $item['DT_FABRIC'] : null;
                        $lote->dt_valid           = $item['DT_VALID'] !== '0000-00-00' ? $item['DT_VALID'] : null;
			$lote->quant_ori          = $item['QUANT_ORI'] !== '' ? $item['QUANT_ORI'] : 0.00;
			$lote->saldo              = $item['SALDO'] !== '' ? $item['SALDO'] : 0.00;
                        $lote->created_at         = new \DateTime();
                        $lote->updated_at         = new \DateTime();
                        $lote->save();
                }
            }

            $log = count($result['result']). ' lotes de produtos cadastrados';
        }


        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


}
