<?php

namespace App\Http\Controllers\Migracao;

//models and controllers
use App\Assinatura;
use App\Produto;

//mails

//framework
use App\EmpresaFilial;
use App\Http\Controllers\Controller;
use App\Utils\Helper;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator;
use Carbon\Carbon;


class ProdutoController extends Controller
{
    //construct
    public function __construct()
    {
        //
    }


    //model create
    public static function migracao()
    {
        $success = true;
        $log     = '';

        error_reporting(-1);//report all errors

        ini_set('display_errors', 1);//display errors to standard output

        $assinatura = Assinatura::first();
        $produto    = new Produto();
        $webservice = $assinatura->webservice_base . $produto->getWebservice().'all';

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
            Produto::where('id','>','0')->delete();

            foreach($result['result'] as $item)
            {
                $empfil = EmpresaFilial::where('filial_erp_id',$item['FILIAL_ID'])->first();

                $produto = new Produto();
                $produto->erp_id             = $item['ERP_ID'];
                $produto->vxgloempfil_id     = isset($empfil) ? $empfil->id : '1';
                $produto->descricao          = $item['DESCRICAO'];
                $produto->tipo               = $item['TIPO'];
                $produto->unidade_principal  = $item['UNIDADE_PRINCIPAL'];
                $produto->unidade_secundaria = $item['UNIDADE_SECUNDARIA'];
                $produto->preco_venda        = $item['PRECO_VENDA'] !== '' ? $item['PRECO_VENDA'] : null;
                $produto->status             = strtolower($item['STATUS']) == 'nao' ? '0' : '1';
                $produto->created_at         = new \DateTime();
                $produto->updated_at         = new \DateTime();
                $produto->save();
            }

            $log = count($result['result']). ' produtos cadastrados';
        }


        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


}