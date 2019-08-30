<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
use App\Cliente;
use App\CreditoCliente;
use App\PedidoVenda;
use App\Http\Controllers\Controller;

//mails

//framework
use Illuminate\Http\Request;

//packages

//extras
use Illuminate\Support\Facades\Auth;
use Validator;


class CreditoClienteController extends Controller
{

    //construct
    public function __construct()
    {
        //
    }


    //adiciona registro do saldo devedor
    public static function adiciona(PedidoVenda $pedido, Cliente $cliente)
    {
        $success = true;
        $log     = [];

        $credito = new CreditoCliente();
        $credito->vxgloempfil_id     = $cliente->vxgloempfil_id;
        $credito->vxglocli_id        = $cliente->id;
        $credito->vxglocli_erp_id    = $cliente->erp_id;
        $credito->vxfatpvenda_id     = $pedido->id;
        $credito->vxfatpvenda_erp_id = $pedido->erp_id;
        $credito->saldo_devedor      = $pedido->valorTotal();
        $credito->created_at         = new \DateTime();
        $credito->updated_at         = new \DateTime();
        $credito->save();

        //atualiza saldo devedor na tabela de cliente
        $cliente->saldo_devedor = $cliente->saldo_devedor + $credito->saldo_devedor;
        $cliente->updated_at    = new \DateTime();
        $cliente->save();

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //atualiza registro do saldo devedor
    public static function atualiza(PedidoVenda $pedido, Cliente $cliente)
    {
        $success = true;
        $log     = [];

        $credito = CreditoCliente::where('vxfatpvenda_id',$pedido->id)->where('vxglocli_id',$cliente->id)->first();

        if(isset($credito))
        {
            //atualiza saldo devedor na tabela de cliente
            if($credito->saldo_devedor > $pedido->valorTotal())
            {
                $diferenca = $cliente->saldo_devedor - $pedido->valorTotal();

                $cliente->saldo_devedor = $cliente->saldo_devedor - $diferenca;
                $cliente->updated_at    = new \DateTime();
                $cliente->save();

            }
            else if($credito->saldo_devedor < $pedido->valorTotal())
            {
                $diferenca = $pedido->valorTotal() - $cliente->saldo_devedor;

                $cliente->saldo_devedor = $cliente->saldo_devedor + $diferenca;
                $cliente->updated_at    = new \DateTime();
                $cliente->save();
            }


            //atualiza saldo devedor na tabela de controle de crédito
            $credito->saldo_devedor = $pedido->valorTotal();
            $credito->updated_at    = new \DateTime();
            $credito->save();
        }
        else
        {
            self::adiciona($pedido, $cliente);
        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //exclui todos os registros com base no cliente
    public static function excluiPorCliente(Cliente $cliente)
    {
        $success = true;
        $log     = [];

        CreditoCliente::where('vxglocli_id',$cliente->id)->delete();

        //OBS: o saldo devedor do cliente será atualizado na própria sincronização com o ERP

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }

}