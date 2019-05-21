<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Assinatura;
use App\EmpresaFilial;
use App\PrecoProduto;
use App\Produto;

//mails

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator; 
use Carbon\Carbon;
use App\Utils\Helper;


class PrecoProdutoController extends Controller
{

    protected $filial;

    //construct
    public function __construct($filialId = null)
    {
        $this->filial = isset($filialId) ? EmpresaFilial::where('filial_erp_id',$filialId)->first() : null;
    }


    public function listaPorProduto($produto_id)
    {
        $success = true;
        $log     = [];

        $produto = Produto::find($produto_id);

        if(!isset($produto))
        {
            $success = false;
            $log[]   = ['error' => 'Produto não encontrado'];
        }
        else 
        {
            $precos = PrecoProduto::join('vx_glo_prod','vx_glo_prod.erp_id','=','vx_fat_tabprc.produto_erp_id')
                ->select('vx_fat_tabprc.*')
                ->where('vx_glo_prod.id',$produto_id)
                ->get();
        }

        $response['success']  = $success;
        $response['log']      = $log;
        $response['produto']  = $produto;
        $response['precos']   = isset($precos) ? $precos : null;
        return $response;
    }


    public function visualiza($preco_produto_id)
    {
        $success = true;
        $log     = [];

        $preco = PrecoProduto::find($preco_produto_id);

        if(!isset($preco))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['preco']   = $preco;
        return $response;
    }


}