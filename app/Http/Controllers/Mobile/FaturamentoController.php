<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\EmpresaFilial;

//mails

//framework
use App\Http\Controllers\Controller;
use App\PedidoVenda;
use App\Vendedor;
use Illuminate\Http\Request;

//packages

//extras


class FaturamentoController extends Controller
{
    protected $filial;
    protected $vendedor;

    //construct
    public function __construct($filialId = null, $user = null)
    {
        $this->filial   = isset($filialId)           ? EmpresaFilial::where('filial_erp_id',$filialId)->first() : null;
        $this->vendedor = isset($user->vxfatvend_id) ? Vendedor::find($user->vxfatvend_id) : null;
    }

    public function dashboard(Request $request)
    {
        $success = true;
        $log     = [];

        $dashboard = new \stdClass();
        $dashboard->pedido_aberto       = PedidoVenda::where('vxfatvend_erp_id',$this->vendedor->erp_id)->where('situacao_pedido','A')->count();
        $dashboard->valor_total_aberto  = 0.00;
        $pedidos = PedidoVenda::where('vxfatvend_erp_id',$this->vendedor->erp_id)->where('situacao_pedido','A')->get();
        foreach($pedidos as $pedido)
        {
            $dashboard->valor_total_aberto = $dashboard->valor_total_aberto + $pedido->valorTotal();
        }
        $dashboard->valor_total_aberto = number_format($dashboard->valor_total_aberto,'2',',','.');

        $response['success']   = $success;
        $response['log']       = $log;
        $response['dashboard'] = $dashboard;
        return $response;
    }


}