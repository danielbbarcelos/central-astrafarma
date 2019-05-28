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
        $configuracao->updated_at   = new \DateTime();
        $configuracao->save();


        /**
         * Salva a logo caso tenha sido enviada
         *
         */
        if($request['logo_empresa'] !== null)
        {
            $path = public_path('uploads/logos/');

            //cria a pasta caso não exista
            if(!File::exists($path)) File::makeDirectory($path,  0775,  true);


            //apaga o arquivo antigo
            if($configuracao->logo_empresa !== null)
            {
                if(File::exists(public_path() . $configuracao->logo_empresa))
                {
                    File::delete(public_path() . $configuracao->logo_empresa);
                }

                $configuracao->logo_empresa = null;
                $configuracao->updated_at   = new \DateTime();
                $configuracao->save();
            }


            $filename = 'logo-'.str_random(20).'.'.$request['logo_empresa']->getClientOriginalExtension();

            File::copy($request['logo_empresa']->getRealPath(), $path . $filename);

            $configuracao->logo_empresa = '/uploads/logos/'. $filename;
            $configuracao->save();


            //altera dinamicamente os valores do .env
            $logoEmpresa = env('LOGO_EMPRESA');
            $logoLocal   = env('LOGO_LOCAL') == true ? 'true' : 'false';


            file_put_contents(app()->environmentFilePath(), str_replace(
                'LOGO_EMPRESA='.$logoEmpresa,'LOGO_EMPRESA='."/uploads/logos/$filename",
                file_get_contents(app()->environmentFilePath())
            ));

            file_put_contents(app()->environmentFilePath(), str_replace(
                'LOGO_LOCAL='.$logoLocal,'LOGO_LOCAL=true',
                file_get_contents(app()->environmentFilePath())
            ));
        }


        $log[] = ['success' => 'Configurações atualizadas com sucesso'];

        $response['success']      = $success;
        $response['log']          = $log;
        $response['configuracao'] = $configuracao;
        return $response;
    }

}
