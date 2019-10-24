<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
use App\Assinatura;
use App\Cidade;
use App\Cliente;
use App\Estado;

//mails

//framework
use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\VexSyncController;
use App\Rules\Cnpj;
use App\Rules\Cpf;
use App\Utils\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//packages

//extras
use Validator;


class ClienteController extends Controller
{

    protected $empfilId;

    //construct
    public function __construct()
    {
         $this->empfilId = Auth::user()->userEmpresaFilial->empfil->id;
    }


    //retorna array do objeto
    public function lista()
    {
        $success = true;
        $log     = [];

        $clientes = Cliente::where(function($query){

            $query->where('vxgloempfil_id',$this->empfilId);
            $query->orWhere('vxgloempfil_id','=',null);

        })->where(function($query){

            if((int)Auth::user()->vxwebperfil_id !== 1)
            {
                $query->where('vxfatvend_erp_id_1',Auth::user()->vendedor->erp_id);
                $query->orWhere('vxfatvend_erp_id_2',Auth::user()->vendedor->erp_id);
            }

        })->orderBy('razao_social','asc')->get();

        $response['success']  = $success;
        $response['log']      = $log;
        $response['clientes'] = $clientes;
        return $response;
    }


    //chamada da tela para adicionar um objeto
    public function adiciona()
    {
        $success = true;
        $log     = [];

        $cliente = new Cliente();

        //busca os estados para vincular ao cadastro
        $estados = Estado::orderBy('uf','asc')->get();

        $cidades = [];

        $response['success']  = $success;
        $response['log']      = $log;
        $response['cliente']  = $cliente;
        $response['estados']  = $estados;
        $response['cidades']  = $cidades;
        return $response;
    }


    //post para adicionar um objeto
    public function adicionaPost(Request $request)
    {
        $success = true;
        $log     = [];

        $request['cnpj_cpf'] = Helper::removeMascara($request['cnpj_cpf']);

        $rules = [
            'cnpj_cpf'      => ['required','unique:vx_glo_cli,cnpj_cpf,NULL,id,deleted_at,NULL', $request['tipo_pessoa'] == 'F' ? new Cpf() : new Cnpj()],
            'razao_social'  => ['required','unique:vx_glo_cli,razao_social,NULL,id,deleted_at,NULL'],
        ];

        $validator = Validator::make($request->all(), $rules, Cliente::$messages);

        if ($validator->fails())
        {
            $success = false;

            foreach($validator->messages()->all() as $message)
            {
                $log[] = ['error' => $message];
            }
        }

        if ($success)
        {
            $cliente = new Cliente();
            $cliente->erp_id             = null;
            $cliente->vxgloempfil_id     = $this->empfilId;
            $cliente->vxfatvend_erp_id_1 = Auth::user()->vendedor->erp_id;
            $cliente->loja               = '01';
            $cliente->tipo_pessoa        = strtoupper($request['tipo_pessoa']);
            $cliente->razao_social       = Helper::formataString($request['razao_social']);
            $cliente->nome_fantasia      = Helper::formataString(isset($request['nome_fantasia']) ? $request['nome_fantasia'] : $request['razao_social']);
            $cliente->cnpj_cpf           = $request['cnpj_cpf'];
            $cliente->contribuinte       = $request['contribuinte'];
            $cliente->insc_estadual     = (int)$cliente->contribuinte == 0 ? 'ISENTO' : Helper::formataString(strtoupper($request['insc_estadual']));
            $cliente->tipo_cliente       = strtoupper($request['tipo_cliente']);
            $cliente->endereco           = Helper::formataString($request['endereco']);
            $cliente->complemento        = Helper::formataString($request['complemento']);
            $cliente->bairro             = Helper::formataString($request['bairro']);
            $cliente->cep                = Helper::removeMascara($request['cep']);
            $cliente->cidade             = Helper::formataString($request['cidade']);
            $cliente->cod_mun            = $request['cidade'] !== null ? Cidade::where('nome',$cliente->cidade)->first()->cod_mun : null;
            $cliente->uf                 = $request['uf'];
            $cliente->ddd                = Helper::removeMascara($request['ddd']);
            $cliente->fone               = Helper::removeMascara($request['fone']);
            $cliente->nome_contato       = Helper::formataString($request['nome_contato']);
            $cliente->email              = $request['email'];
            $cliente->email_con          = $request['email_con'];
            $cliente->email_fin          = $request['email_fin'];
            $cliente->envia_boleto       = $request['envia_boleto'];
            $cliente->obs_nota           = Helper::formataString($request['obs_nota'] !== null ? $request['obs_nota'] : '');
            $cliente->obs_interna        = Helper::formataString($request['obs_interna'] !== null ? $request['obs_interna'] : '');
            $cliente->risco              = 'E';
            $cliente->status             = isset($request['status']) ? $request['status'] : 0;
            $cliente->created_at         = new \DateTime();
            $cliente->updated_at         = new \DateTime();
            $cliente->save();


            //gera vex sync
            VexSyncController::adiciona(Helper::formataTenantId($this->empfilId), 'post', $cliente->getTable(), $cliente->id, $cliente->getWebservice('add'));

            $log[]   = ['success' => 'Cliente cadastrado com sucesso'];

        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //chamada da tela para editar um objeto
    public function edita($cliente_id)
    {
        $success = true;
        $log     = [];

        $cliente = Cliente::where('id',$cliente_id)->where(function($query){

            $query->where('vxgloempfil_id',$this->empfilId);
            $query->orWhere('vxgloempfil_id','=',null);

        })->where(function($query){

            if((int)Auth::user()->vxwebperfil_id !== 1)
            {
                $query->where('vxfatvend_erp_id_1',Auth::user()->vendedor->erp_id);
                $query->orWhere('vxfatvend_erp_id_2',Auth::user()->vendedor->erp_id);
            }

        })->first();

        if(!isset($cliente))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }

        //busca os estados para vincular ao cadastro
        $estados = Estado::orderBy('uf','asc')->get();

        $cidades = isset($cliente->cidade) ? Cidade::orderBy('nome','asc')->get() : [];

        $response['success'] = $success;
        $response['log']     = $log;
        $response['cliente'] = $cliente;
        $response['estados'] = $estados;
        $response['cidades'] = $cidades;
        return $response;
    }


    //post para editar um objeto
    public function editaPost(Request $request, $cliente_id)
    {
        $success = true;
        $log     = [];

        $cliente = Cliente::find($cliente_id);

        if(!isset($cliente))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            $request['cnpj_cpf'] = Helper::removeMascara($request['cnpj_cpf']);

            $rules = [
                'cnpj_cpf'      => ['required','unique:vx_glo_cli,cnpj_cpf,'.$cliente_id.',id,deleted_at,NULL', $request['tipo_pessoa'] == 'F' ? new Cpf() : new Cnpj()],
                'razao_social'  => ['required','unique:vx_glo_cli,razao_social,'.$cliente_id.',id,deleted_at,NULL'],
            ];

            $validator = Validator::make($request->all(), $rules, Cliente::$messages);

            if ($validator->fails())
            {
                $success = false;

                foreach($validator->messages()->all() as $message)
                {
                    $log[] = ['error' => $message];
                }
            }

            if ($success)
            {
                $cliente->tipo_pessoa       = strtoupper($request['tipo_pessoa']);
                $cliente->razao_social      = Helper::formataString($request['razao_social']);
                $cliente->nome_fantasia     = Helper::formataString(isset($request['nome_fantasia']) ? $request['nome_fantasia'] : $request['razao_social']);
                $cliente->cnpj_cpf          = $request['cnpj_cpf'];
                $cliente->contribuinte      = $request['contribuinte'];
                $cliente->insc_estadual     = (int)$cliente->contribuinte == 0 ? 'ISENTO' : Helper::formataString(strtoupper($request['insc_estadual']));
                $cliente->tipo_cliente      = strtoupper($request['tipo_cliente']);
                $cliente->endereco          = Helper::formataString($request['endereco']);
                $cliente->complemento       = Helper::formataString($request['complemento']);
                $cliente->bairro            = Helper::formataString($request['bairro']);
                $cliente->cep               = Helper::removeMascara($request['cep']);
                $cliente->cidade            = Helper::formataString($request['cidade']);
                $cliente->cod_mun           = $request['cidade'] !== null ? Cidade::where('nome',$cliente->cidade)->first()->cod_mun : null;
                $cliente->uf                = $request['uf'];
                $cliente->ddd               = Helper::removeMascara($request['ddd']);
                $cliente->fone              = Helper::removeMascara($request['fone']);
                $cliente->nome_contato      = Helper::formataString($request['nome_contato']);
                $cliente->email             = $request['email'];
                $cliente->email_con         = $request['email_con'];
                $cliente->email_fin         = $request['email_fin'];
                $cliente->envia_boleto      = $request['envia_boleto'];
                $cliente->obs_nota          = Helper::formataString($request['obs_nota'] !== null ? $request['obs_nota'] : '');
                $cliente->obs_interna       = Helper::formataString($request['obs_interna'] !== null ? $request['obs_interna'] : '');
                $cliente->status            = isset($request['status']) ? $request['status'] : 0;
                $cliente->updated_at        = new \DateTime();
                $cliente->save();


                //gera vex sync
                VexSyncController::adiciona(Helper::formataTenantId($this->empfilId), 'put', $cliente->getTable(), $cliente->id, $cliente->getWebservice('edit/'.$cliente->erp_id));

                $log[]   = ['success' => 'Cliente atualizado com sucesso'];

            }

        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //chamada da tela para visualizar um objeto
    public function visualiza($cliente_id)
    {
        $success = true;
        $log     = [];

        $cliente = Cliente::where('id',$cliente_id)->where(function($query){

            $query->where('vxgloempfil_id',$this->empfilId);
            $query->orWhere('vxgloempfil_id','=',null);

        })->where(function($query){

            if((int)Auth::user()->vxwebperfil_id !== 1)
            {
                $query->where('vxfatvend_erp_id_1',Auth::user()->vendedor->erp_id);
                $query->orWhere('vxfatvend_erp_id_2',Auth::user()->vendedor->erp_id);
            }

        })->first();

        if(!isset($cliente))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }

        //busca os estados para vincular ao cadastro
        $estados = Estado::orderBy('uf','asc')->get();

        $cidades = isset($cliente->cidade) ? Cidade::orderBy('nome','asc')->get() : [];

        $response['success'] = $success;
        $response['log']     = $log;
        $response['cliente'] = $cliente;
        $response['estados'] = $estados;
        $response['cidades'] = $cidades;
        return $response;
    }

}