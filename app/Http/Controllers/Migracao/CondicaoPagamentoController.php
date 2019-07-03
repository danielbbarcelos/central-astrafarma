<?php

namespace App\Http\Controllers\Migracao;

//models and controllers
use App\Assinatura;
use App\Cliente;

//mails

//framework
use App\CondicaoPagamento;
use App\EmpresaFilial;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
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
    public static function migracao($uri = 'all')
    {
        $success = true;
        $log     = '';

        error_reporting(-1);//report all errors

        ini_set('display_errors', 1);//display errors to standard output

        $assinatura = Assinatura::first();
        $condicao   = new CondicaoPagamento();
        $webservice = $assinatura->webservice_base . $condicao->getWebservice() . $uri;

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
            CondicaoPagamento::truncate();

            foreach($result['result'] as $item)
            {
                $empfil = EmpresaFilial::where('filial_erp_id',$item['FILIAL_ID'])->first();

                $condicao = new CondicaoPagamento();
                $condicao->erp_id             = $item['ERP_ID'];
                $condicao->vxgloempfil_id     = isset($empfil) ? $empfil->id : '1';
                $condicao->descricao          = $item['DESCRICAO'];
                $condicao->web                = strtolower($item['STATUS']) == 'nao' ? '0' : '1';
                $condicao->mobile             = strtolower($item['STATUS']) == 'nao' ? '0' : '1';
                $condicao->status             = strtolower($item['STATUS']) == 'nao' ? '0' : '1';
                $condicao->created_at         = new \DateTime();
                $condicao->updated_at         = new \DateTime();
                $condicao->save();
            }

            $log = count($result['result']). ' condições de pagamento cadastradas';
        }


        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


}