<?php

namespace App\Http\Controllers\Migracao;

//models and controllers
use App\Assinatura;
use App\Vendedor;

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


class VendedorController extends Controller
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
        $vendedor   = new Vendedor();
        $webservice = $assinatura->webservice_base . $vendedor->getWebservice() . $uri;

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
            Vendedor::truncate();

            foreach($result['result'] as $item)
            {
                $empfil = EmpresaFilial::where('filial_erp_id',$item['FILIAL_ID'])->first();

                $vendedor = new Vendedor();
                $vendedor->erp_id         = $item['ERP_ID'];
                $vendedor->vxgloempfil_id = isset($empfil) ? $empfil->id : '1';
                $vendedor->nome           = $item['NOME'];
                $vendedor->cpf            = $item['CPF'] !== '' ? $item['CPF'] : null;
                $vendedor->status         = strtolower($item['STATUS']) == 'nao' ? '0' : '1';
                $vendedor->email          = $item['EMAIL'] !== '' ? $item['EMAIL'] : null;
                $vendedor->fone           = $item['FONE'] !== '' ? $item['FONE'] : null;
                $vendedor->created_at     = new \DateTime();
                $vendedor->updated_at     = new \DateTime();
                $vendedor->save();
            }

            $log = count($result['result']). ' vendedores cadastrados';
        }


        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


}