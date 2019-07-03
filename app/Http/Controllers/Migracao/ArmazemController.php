<?php

namespace App\Http\Controllers\Migracao;

//models and controllers
use App\Armazem;
use App\Assinatura;

//mails

//framework
use App\EmpresaFilial;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator;
use Carbon\Carbon;


class ArmazemController extends Controller
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
        $armazem    = new Armazem();
        $webservice = $assinatura->webservice_base . $armazem->getWebservice() . $uri;

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
            Armazem::truncate();

            foreach($result['result'] as $item)
            {
                $empfil = EmpresaFilial::where('filial_erp_id',$item['FILIAL_ID'])->first();

                $armazem = new Armazem();
                $armazem->erp_id             = $item['ERP_ID'];
                $armazem->vxgloempfil_id     = isset($empfil) ? $empfil->id : '1';
                $armazem->descricao          = $item['DESCRICAO'];
                $armazem->created_at         = new \DateTime();
                $armazem->updated_at         = new \DateTime();
                $armazem->save();
            }

            $log = count($result['result']). ' armazéns cadastrados';
        }


        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


}