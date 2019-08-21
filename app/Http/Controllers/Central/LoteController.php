<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
use App\Http\Controllers\Controller;
use App\Lote;

//mails

//framework

use App\PedidoItem;
use Illuminate\Http\Request;

//packages

//extras
use Illuminate\Support\Facades\Auth;
use Validator;


class LoteController extends Controller
{

    protected $empfilId;

    //construct
    public function __construct()
    {
        $this->empfilId = Auth::user()->userEmpresaFilial->empfil->id;
    }


    //atualiza quantidade empenhada por lote
    public static function atualizaQuantidadeEmpenhada($erp_id)
    {
        $success = true;
        $log     = [];

        $quantidade = 0.00;

        $itens = PedidoItem::join('vx_fat_pvenda','vx_fat_pvenda.id','=','vx_fat_ipvend.vxfatpvenda_id')
            ->select('vx_fat_ipvend.quantidade')
            ->where('vx_fat_pvenda.erp_id',null)
            ->where('vxestlote_erp_id',$erp_id)
            ->get();

        foreach($itens as $item)
        {
            $quantidade = $quantidade + $item->quantidade;
        }

        $lote = Lote::where('erp_id',$erp_id)->first();

        if(isset($lote))
        {
            $lote->empenho    = $quantidade;
            $lote->updated_at = new \DateTime();
            $lote->save();
        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //retorna quantidade empenhada com excessão do pedido informado
    public static function confirmaQuantidadeEmpenhada($lote_erp_id, $pedido_id)
    {
        $success = true;
        $log     = [];
        $empenho = 0.00;

        $lote = Lote::where('erp_id',$lote_erp_id)->first();

        if(isset($lote))
        {
            $itens = PedidoItem::where('vxfatpvenda_id',$pedido_id)->where('vxestlote_erp_id',$lote_erp_id)->get();

            foreach ($itens as $item)
            {
                $lote->empenho = $lote->empenho - $item->quantidade;
            }

            $empenho = $lote->empenho;
        }


        $response['success'] = $success;
        $response['log']     = $log;
        $response['empenho'] = $empenho;
        return $response;
    }

}