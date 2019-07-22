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
use App\TabelaPreco;
use App\TabelaPrecoProduto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator; 
use Carbon\Carbon;
use App\Utils\Helper;


class TabelaPrecoProdutoController extends Controller
{

    protected $filial;

    //construct
    public function __construct($filialId = null)
    {
        $this->filial = isset($filialId) ? EmpresaFilial::where('filial_erp_id',$filialId)->first() : null;
    }


    public function busca($tabela_id, $uf, $produto_id)
    {
        $success = true;
        $log     = [];

        $preco = TabelaPrecoProduto::where('vxfattabprc_id',$tabela_id)
            ->where('uf',strtoupper($uf))
            ->where('data_vigencia','!=',null)
            ->where('data_vigencia','>=',Carbon::now()->format('Y-m-d'))
            ->where('vxgloprod_id',$produto_id)
            ->first();


        if(!isset($preco))
        {
            $success = false;
            $log[]   = ['error' => 'Nenhum preço disponível com os parâmetros informados'];
        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['preco']   = isset($preco) ? $preco : null;
        return $response;
    }

}
