<?php

namespace App\Http\Controllers\Migracao;

//models and controllers
use App\Armazem;
use App\Assinatura;

//mails

//framework
use App\EmpresaFilial;
use App\Http\Controllers\Controller;
use App\TabelaPreco;
use App\TabelaPrecoArmazem;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator;
use Carbon\Carbon;


class TabelaPrecoArmazemController extends Controller
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
        $tpArmazem  = new TabelaPrecoArmazem();
        $webservice = $assinatura->webservice_base . $tpArmazem->getWebservice() . $uri;

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
            TabelaPrecoArmazem::truncate();
	    
            foreach($result['result'] as $item)
            {
                $empfil  = EmpresaFilial::where('filial_erp_id',$item['FILIAL_ID'])->first();

                $tabela  = TabelaPreco::where('erp_id',$item['VXFATTABPRC_ERP_ID'])->first();

                $armazem = Armazem::where('erp_id',$item['VXESTARMZ_ERP_ID'])->first();

                $tpArmazem = new TabelaPrecoArmazem();
                $tpArmazem->erp_id             = $item['ERP_ID'];
                $tpArmazem->vxgloempfil_id     = isset($empfil)  ? $empfil->id : '1';
                $tpArmazem->vxfattabprc_id     = isset($tabela)  ? $tabela->id : null;
                $tpArmazem->vxfattabprc_erp_id = isset($tabela)  ? $tabela->erp_id : null;
                $tpArmazem->vxestarmz_id       = isset($armazem) ? $armazem->id : null;
                $tpArmazem->vxestarmz_erp_id   = isset($armazem) ? $armazem->erp_id : null;
                $tpArmazem->created_at         = new \DateTime();
                $tpArmazem->updated_at         = new \DateTime();
                $tpArmazem->save();
            }

            $log = count($result['result']). ' registrados cadastrados';
        }


        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


}
