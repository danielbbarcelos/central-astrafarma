<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Assinatura;
use App\CondicaoPagamento;

//mails

//framework
use App\EmpresaFilial;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages

//extras
use Validator; 
use App\Utils\Helper;


class CondicaoPagamentoController extends Controller
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

        $condicoes = CondicaoPagamento::where(function($query){
            if($this->filial !== null)
            {
                $query->where('vxgloempfil_id',$this->filial->id);
                $query->orWhere('vxgloempfil_id',null);
            }
        })->where('status','1')->orderBy('descricao','asc')->get();

        $response['success']   = $success;
        $response['log']       = $log;
        $response['condicoes'] = $condicoes;
        return $response;
    }


}