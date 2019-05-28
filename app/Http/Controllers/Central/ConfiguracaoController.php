<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
use App\Configuracao;

//mails

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

//packages

//extras
use Validator;


class ConfiguracaoController extends Controller
{

    protected $empfilId;
    protected $vendedorId;

    //construct
    public function __construct()
    {
        $this->empfilId   = Auth::user()->userEmpresaFilial->empfil->id;
        $this->vendedorId = isset(Auth::user()->vendedor) ? Auth::user()->vendedor->id : '1';
    }


    //chamada da tela de configurações gerais do sistema
    public function visualiza()
    {
        $success = true;
        $log     = [];

        $configuracao = Configuracao::where('vxgloempfil_id',$this->empfilId)->first();

        if(!isset($configuracao))
        {
            $configuracao = new Configuracao();
            $configuracao->vxgloempfil_id   = $this->empfilId;
            $configuracao->created_at       = new \DateTime();
            $configuracao->updated_at       = new \DateTime();
            $configuracao->save();
        }

        $response['success']      = $success;
        $response['log']          = $log;
        $response['configuracao'] = $configuracao;
        return $response;
    }

    //post para atualizar configurações gerais
    public function editaPost(Request $request)
    {
        $success = true;
        $log     = [];

        $configuracao = Configuracao::where('vxgloempfil_id',$this->empfilId)->first();

        if(!isset($configuracao))
        {
            $configuracao = new Configuracao();
            $configuracao->created_at = new \DateTime();
        }

        $configuracao->pdf_template = $request['pdf_template'];
        $configuracao->logo_empresa = $request['logo_empresa']; //tratar upload da logo
        $configuracao->updated_at   = new \DateTime();
        $configuracao->save();

        $log[] = ['success' => 'Configurações atualizadas com sucesso'];

        $response['success']      = $success;
        $response['log']          = $log;
        $response['configuracao'] = $configuracao;
        return $response;
    }

}
