<?php

namespace App\Http\Controllers\Migracao;

//models and controllers
use App\Assinatura;
use App\Cliente;

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


class ClienteController extends Controller
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
        $cliente    = new Cliente();
        $webservice = $assinatura->webservice_base . $cliente->getWebservice() . $uri;

        $guzzle  = new Client();
        $result  = $guzzle->request('GET', $webservice);

        $result  = mb_convert_encoding($result->getBody(), 'UTF-8');

        $result  = json_decode($result, true);


        if($result == null)
        {
            $success = false;
            $log     = 'Não foi possível importar os registros encontrados';
        }
        else
        {
            Cliente::truncate();

            foreach($result['result'] as $item)
            {
                $empfil = EmpresaFilial::where('filial_erp_id',$item['FILIAL_ID'])->first();

                if(!isset($item['STATUS']))
                {
                    $status = '1';
                }
                else
                {
                    $status = strtolower($item['STATUS']) == 'nao' ? '0' : '1';
                }

                $cliente = new Cliente();
                $cliente->erp_id         = $item['ERP_ID'];
                $cliente->vxgloempfil_id = isset($empfil) ? $empfil->id : '1';
                $cliente->tipo_pessoa    = $item['TIPO_PESSOA'];
                $cliente->razao_social   = $item['RAZAO_SOCIAL'];
                $cliente->nome_fantasia  = $item['NOME_FANTASIA'];
                $cliente->cnpj_cpf       = Helper::removeMascara($item['CNPJ_CPF']);
                $cliente->loja           = $item['LOJA'];
                $cliente->tipo_cliente   = $item['TIPO_CLIENTE'];
                $cliente->endereco       = $item['ENDERECO'];
                $cliente->bairro         = $item['BAIRRO'];
                $cliente->cep            = Helper::removeMascara($item['CEP']);
                $cliente->cod_mun        = $item['COD_MUN'];
                $cliente->cidade         = $item['CIDADE'];
                $cliente->uf             = $item['UF'];
                $cliente->ddd            = (int)$item['DDD'];
                $cliente->fone           = $item['FONE'];
                $cliente->nome_contato   = $item['NOME_CONTATO'];
                $cliente->email          = $item['EMAIL'];
                $cliente->status         = $status;
                $cliente->created_at     = new \DateTime();
                $cliente->updated_at     = new \DateTime();
                $cliente->save();
            }


            $log = count($result['result']). ' clientes cadastrados';
        }


        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


}