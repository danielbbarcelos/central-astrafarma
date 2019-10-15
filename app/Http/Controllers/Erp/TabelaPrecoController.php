<?php 

namespace App\Http\Controllers\Erp; 

//models and controllers
use App\EmpresaFilial;
use App\TabelaPreco;

//mails

//framework
use App\Http\Controllers\Controller;
use App\Produto;
use App\TabelaPrecoProduto;
use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

//packages

//extras
use Validator; 
use Carbon\Carbon;

class TabelaPrecoController extends Controller
{

    private static $logMessage = "Execução de VEX Sync em Erp\TabelaPrecoController\n\n";


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


            //armazena os produtos em uma variável temporária
            $produtos = $vars['produtos'];
            unset($vars['produtos']);


            //inclui timestamps
            $vars['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $vars['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //status automatico
            $vars['status'] = '1';


            $tabela = new TabelaPreco();
            $id     = $tabela->insertGetId($vars);
            $tabela = TabelaPreco::find($id);

            //gera o relacionamento entre produtos e tabela de preço
            foreach($produtos as $item)
            {
                $item    = Helper::retornoERP((object)$item);
                $item    = json_decode($item);
                $produto = Produto::where('erp_id', $item->vxgloprod_erp_id)->first();

                //caso o produto não esteja no banco de dados, buscamos o cadastro dele
                if(!isset($produto))
                {
                    $object = new \stdClass();
                    $object->tabela = 'vx_glo_prod';
                    $object->ws = '/rest/vxgloprod/'.$item->vxgloprod_erp_id;

                    VexSyncController::create($object);

                    $produto = Produto::where('erp_id', $item->vxgloprod_erp_id)->first();
                }

                $preco = new TabelaPrecoProduto();
                $preco->vxfattabprc_id      = $tabela->id;
                $preco->vxfattabprc_erp_id  = $tabela->erp_id;
                $preco->vxgloprod_id        = $produto->id;
                $preco->vxgloprod_erp_id    = $produto->erp_id;
                $preco->uf                  = $item->uf;
                $preco->preco_venda         = number_format((float) $item->preco_venda,2,'.','');
                $preco->preco_maximo        = number_format((float) $item->preco_maximo,2,'.','');
                $preco->valor_desconto      = number_format((float) $item->valor_desconto,2,'.','');
                $preco->fator               = number_format((float) $item->fator,2,'.','');
                $preco->created_at          = new \DateTime();
                $preco->updated_at          = new \DateTime();
                $preco->save();
            }

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


            //armazena os produtos em uma variável temporária
            $produtos = $vars['produtos'];
            unset($vars['produtos']);


            //inclui timestamps
            $vars['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

            //status automatico
            $vars['status'] = '1';

            TabelaPreco::where('vxgloempfil_id', isset($vars['vxgloempfil_id']) ? $vars['vxgloempfil_id'] : null)
                ->where('erp_id',$vars['erp_id'])
                ->update($vars);

            $tabela = TabelaPreco::where('erp_id',$vars['erp_id'])->first();
            

            foreach($produtos as $item)
            {
                $item    = Helper::retornoERP((object)$item);
                $item    = json_decode($item);
                $produto = Produto::where('erp_id', $item->vxgloprod_erp_id)->first();

                //caso o produto não esteja no banco de dados, buscamos o cadastro dele
                if(!isset($produto))
                {
                    $object = new \stdClass();
                    $object->tabela = 'vx_glo_prod';
                    $object->ws = '/rest/vxgloprod/'.$item->vxgloprod_erp_id;

                    VexSyncController::create($object);

                    $produto = Produto::where('erp_id', $item->vxgloprod_erp_id)->first();
                }


                $preco = TabelaPrecoProduto::where('uf',$item->uf)->where('vxfattabprc_id',$tabela->id)->where('vxgloprod_id',$produto->id)->first();

                if(!isset($preco))
                {
                    $preco = new TabelaPrecoProduto();
                    $preco->vxfattabprc_id      = $tabela->id;
                    $preco->vxfattabprc_erp_id  = $tabela->erp_id;
                    $preco->vxgloprod_id        = $produto->id;
                    $preco->vxgloprod_erp_id    = $produto->erp_id;
                    $preco->uf                  = $item->uf;
                    $preco->created_at          = new \DateTime();
                }

                $preco->data_vigencia       = ($item->data_vigencia !== null and $item->data_vigencia !== '' and $item->data_vigencia !== '0000-00-00') ? $item->data_vigencia : null;
                $preco->preco_venda         = number_format((float) $item->preco_venda,2,'.','');
                $preco->preco_maximo        = number_format((float) $item->preco_maximo,2,'.','');
                $preco->valor_desconto      = number_format((float) $item->valor_desconto,2,'.','');
                $preco->fator               = number_format((float) $item->fator,2,'.','');
                $preco->updated_at          = new \DateTime();
                $preco->save();
            }

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
            if(strlen($vars['erp_id']) > 3)
            {
                $erp_id         = substr($vars['erp_id'],0,3);
                $produto_erp_id = substr($vars['erp_id'],3);
            }
            else
            {
                $erp_id         = $vars['erp_id'];
                $produto_erp_id = null;
            }

            $tabela = TabelaPreco::where('vxgloempfil_id', isset($empfil) ? $empfil->id : null)
                ->where('erp_id',$vars['erp_id'])
                ->first();

            //exclui o relacionamento com os produtos
            TabelaPrecoProduto::where('vxfattabprc_id',$tabela->id)->delete();

            $tabela->delete();

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