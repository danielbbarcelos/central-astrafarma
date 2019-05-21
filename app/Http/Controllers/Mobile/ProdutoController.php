<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Assinatura;
use App\EmpresaFilial;
use App\Produto;

//mails

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator; 
use App\Utils\Helper;


class ProdutoController extends Controller
{
    protected $filial;

    //construct
    public function __construct($filialId = null)
    {
        $this->filial = isset($filialId) ? EmpresaFilial::where('filial_erp_id',$filialId)->first() : null;
    }


    public function lista()
    {
        $success = true;
        $log     = [];

        $filial  = $this->filial;

        $produtos = Produto::where(function($query) use ($filial){
            if($filial !== null)
            {
                $query->where('vxgloempfil_id',$filial->id);
                $query->orWhere('vxgloempfil_id',null);
            }
        })->where('status','1')->orderBy('descricao','asc')->get();

        $response['success'] = $success;
        $response['log']     = $log;
        $response['produtos'] = $produtos;
        return $response;
    }


    public function visualiza($produto_id)
    {
        $success = true;
        $log     = [];

        $produto = Produto::find($produto_id);

        if(!isset($produto))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['produto'] = $produto;
        return $response;
    }


}