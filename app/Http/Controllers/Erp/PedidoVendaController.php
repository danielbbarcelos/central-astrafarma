<?php 

namespace App\Http\Controllers\Erp; 

//models and controllers
use App\EmpresaFilial;
use App\PedidoVenda;
use App\PedidoItem;

//mails

//framework
use App\Http\Controllers\Controller;
use App\Produto;
use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

//packages

//extras
use Illuminate\Support\Facades\Log;
use Validator;
use Carbon\Carbon;

class PedidoVendaController extends Controller
{

    private static $logMessage = "Execução de VEX Sync em Erp\PedidoVendaController\n\n";


    //construct
    public function __construct()
    {
        //
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
            unset($vars['vex_id']);

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

            //retira a variavel itens do pedido, para ser tratada separadamente
            $itens = $vars['itens'];

            unset($vars['itens']);

            $pedido = PedidoVenda::where('vxgloempfil_id', isset($vars['vxgloempfil_id']) ? $vars['vxgloempfil_id'] : null)
                ->where('erp_id',$vars['erp_id'])
                ->first();

            if(!isset($pedido))
            {
                $success = false;
                $log     = 'Pedido não encontrado';
            }
            else
            {
                PedidoVenda::where('vxgloempfil_id', isset($vars['vxgloempfil_id']) ? $vars['vxgloempfil_id'] : null)
                    ->where('erp_id',$vars['erp_id'])
                    ->update($vars);

                //exclui os itens do pedido para adicioná-los novamente
                PedidoItem::where('vxfatpvenda_id', $pedido->id)->forceDelete();

                if(isset($itens))
                { 
                    $itens = Helper::retornoERP($itens);
		
                    foreach(json_decode($itens) as $item)
                    {
                        $produto = Produto::where('erp_id',$item->vxgloprod_erp_id)->first();

                        $pedidoItem = new PedidoItem();
                        $pedidoItem->vxfatpvenda_id     = $pedido->id;
                        $pedidoItem->vxfatpvenda_erp_id = $vars['erp_id'];
                        $pedidoItem->vxgloprod_erp_id   = $item->vxgloprod_erp_id;
                        $pedidoItem->vxfattabprc_erp_id = $item->vxfattabprc_erp_id;
                        $pedidoItem->vxestarmz_erp_id   = $item->vxestarmz_erp_id;
                        $pedidoItem->vxestlote_erp_id   = $item->vxestlote_erp_id;
                        $pedidoItem->quantidade         = $item->quantidade;
                        $pedidoItem->alerta_estoque     = $item->alerta_estoque;
                        $pedidoItem->produto_data       = json_encode($produto, JSON_UNESCAPED_UNICODE);
                        $pedidoItem->preco_unitario     = Helper::formataDecimal($item->preco_unitario);
                        $pedidoItem->preco_venda        = Helper::formataDecimal($item->preco_venda);
                        $pedidoItem->valor_desconto     = Helper::formataDecimal($item->valor_desconto);
                        $pedidoItem->valor_total        = Helper::formataDecimal($item->valor_total);
                        $pedidoItem->nota_fiscal        = isset($item->nota_fiscal) ? $item->nota_fiscal : null;
                        $pedidoItem->serienf            = isset($item->serienf) ? $item->serienf : null;
                        $pedidoItem->data_nf            = ($item->data_nf !== '' and $item->data_nf !== '0000-00-00') ? $item->data_nf : null;
                        $pedidoItem->created_at         = new \DateTime();
                        $pedidoItem->updated_at         = new \DateTime();
                        $pedidoItem->save();
                    }
                }
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
            $pedido = PedidoVenda::where('vxgloempfil_id', isset($empfil) ? $empfil->id : null)
                ->where('erp_id',$vars['erp_id'])
                ->first();

            $id = $pedido->id;

            $pedido->delete();

            PedidoItem::where('vxfatpvenda_id', $id)->delete();

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
