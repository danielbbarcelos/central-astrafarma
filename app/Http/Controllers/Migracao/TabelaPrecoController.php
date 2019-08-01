<?php

namespace App\Http\Controllers\Migracao;

//models and controllers
use App\Assinatura;
use App\Cliente;

//mails

//framework
use App\EmpresaFilial;
use App\Http\Controllers\Controller;
use App\Produto;
use App\TabelaPreco;
use App\TabelaPrecoProduto;
use App\Utils\Helper;
use App\Vendedor;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Validator;
use Carbon\Carbon;


class TabelaPrecoController extends Controller
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

        set_time_limit(600);

        ini_set('display_errors', 1);//display errors to standard output

        $assinatura = Assinatura::first();
        $tabela     = new TabelaPreco();
        $webservice = $assinatura->webservice_base . $tabela->getWebservice() . $uri;

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
            TabelaPreco::where('id','>','0')->where('erp_id',$result['result']['ERP_ID'])->forceDelete();
            TabelaPrecoProduto::where('id','>','0')->where('vxfattabprc_erp_id',$result['result']['ERP_ID'])->forceDelete();

            if(File::exists(storage_path('logs/vex-sync-migracao-'.$tabela->getWebservice().'.log')))
            {
                File::delete(storage_path('logs/vex-sync-migracao-'.$tabela->getWebservice().'.log'));
            }



            $empfil = EmpresaFilial::where('filial_erp_id',$result['result']['FILIAL_ID'])->first();

            $tabela = new TabelaPreco();
            $tabela->erp_id         = $result['result']['ERP_ID'];
            $tabela->vxgloempfil_id = isset($empfil) ? $empfil->id : '1';
            $tabela->descricao      = $result['result']['DESCRICAO'];
            $tabela->status         = '1';
            $tabela->created_at     = new \DateTime();
            $tabela->updated_at     = new \DateTime();
            $tabela->save();

            $index = 1;


            $message  = "TABELA DE PREÇO {$result['result']['ERP_ID']} \n\n";
            $message .= "QUANTIDADE DE PRODUTOS ".count($result['result']['PRODUTOS'])." \n\n";

            Helper::logFile('vex-sync-migracao-'.$tabela->getTable().'.log', $message);


            foreach ($result['result']['PRODUTOS'] as $item)
            {
                $message = "ERP ID do produto: ".$item['VXGLOPROD_ERP_ID']." \n\n";

                $produto = Produto::where('erp_id', $item['VXGLOPROD_ERP_ID'])->first();

                if(isset($produto))
                {
                    try
                    {
                        $preco = new TabelaPrecoProduto();
                        $preco->vxfattabprc_id      = $tabela->id;
                        $preco->vxfattabprc_erp_id  = $tabela->erp_id;
                        $preco->vxgloprod_id        = $produto->id;
                        $preco->vxgloprod_erp_id    = $produto->erp_id;
                        $preco->data_vigencia       = ($item['DATA_VIGENCIA'] !== null and $item['DATA_VIGENCIA'] and $item['DATA_VIGENCIA'] !== '0000-00-00') ? $item['DATA_VIGENCIA'] : null;
                        $preco->preco_venda         = $item['PRECO_VENDA'];
                        $preco->preco_maximo        = $item['PRECO_MAXIMO'];
                        $preco->valor_desconto      = $item['VALOR_DESCONTO'];
                        $preco->fator               = $item['FATOR'];
                        $preco->uf                  = $item['UF'];
                        $preco->created_at = new \DateTime();
                        $preco->updated_at = new \DateTime();
                        $preco->save();

                        $message .= "STATUS DA MIGRAÇÃO: SUCESSO\n\n";
                    }
                    catch (\Exception $e)
                    {
                        $message .= "STATUS DA MIGRAÇÃO: ERRO\n\n";
                        $message .= 'MOTIVO: Code '.$e->getFile().' - File: '.$e->getFile().' ('.$e->getLine().') - Message: '.$e->getMessage()."\n\n";
                    }

                }
                else
                {
                    $message .= "STATUS DA MIGRAÇÃO: ERRO\n\n";
                    $message .= "MOTIVO: produto não encontrado via ERP_ID \n\n";
                }


                //salva log de sincronização
                Helper::logFile('vex-sync-migracao-'.$tabela->getTable().'.log', $message);

                $index++;

            }

            $log = count($result['result']['PRODUTOS']). ' produtos atualizados pela tabela de preço';
        }


        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


}