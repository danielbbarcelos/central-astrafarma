<?php 

namespace App\Http\Controllers\Erp; 

//models and controllers
use App\EmpresaFilial;
use App\PedidoVenda;
use App\PedidoItem;

//mails

//framework
use App\Http\Controllers\Controller;
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
    //construct
    public function __construct()
    {
        //
    }


    //model update
    public static function update($vars)
    {
        $success = true;
        $log     = '';

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

                    foreach($itens as $item)
                    {
                        $pedidoItem = new PedidoItem();
                        $pedidoItem->vxfatpvenda_id     = $pedido->id;
                        $pedidoItem->vxfatpvenda_erp_id = $vars['erp_id'];
                        $pedidoItem->vxgloprod_erp_id   = $item['vxgloprod_erp_id'];
                        $pedidoItem->quantidade         = $item['quantidade'];
                        $pedidoItem->preco_unitario     = Helper::formataDecimal($item['preco_unitario']);
                        $pedidoItem->preco_venda        = Helper::formataDecimal($item['preco_venda']);
                        $pedidoItem->valor_total        = Helper::formataDecimal($item['valor_total']);
                        $pedidoItem->nota_fiscal        = isset($item['nota_fiscal']) ? $item['nota_fiscal'] : null;
                        $pedidoItem->serienf            = isset($item['serienf']) ? $item['serienf'] : null;
                        $pedidoItem->created_at         = new \DateTime();
                        $pedidoItem->updated_at         = new \DateTime();
                        $pedidoItem->save();
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $success = false;
            $log     = 'Ocorreu um erro ao processar os itens';

	        Log::info($e->getFile().' : '.$e->getLine().' - '.$e->getMessage());
        }
        
        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }




    //model delete
    public static function delete($vars, EmpresaFilial $empfil = null)
    {
        $success = true;
        $log     = '';

        try
        {
            $pedido = PedidoVenda::where('vxgloempfil_id', isset($empfil) ? $empfil->id : null)
                ->where('erp_id',$vars['erp_id'])
                ->first();

            $id = $pedido->id;

            $pedido->delete();

            PedidoItem::where('vxfatpvenda_id', $id)->delete();
        }
        catch(\Exception $e)
        {
            $success = false;
            $log     = 'Ocorreu um erro ao processar os itens';
        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }

}
