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
            $cliente->erp_id            = null;
            $cliente->vxgloempfil_id    = $this->empfilId;
            $cliente->loja              = null;
            $cliente->tipo_pessoa       = strtoupper($request['tipo_pessoa']);
            $cliente->razao_social      = $request['razao_social'];
            $cliente->nome_fantasia     = isset($request['nome_fantasia']) ? $request['nome_fantasia'] : $request['razao_social'];
            $cliente->cnpj_cpf          = $request['cnpj_cpf'];
            $cliente->tipo_cliente      = strtoupper($request['tipo_cliente']);
            $cliente->endereco          = $request['endereco'];
            $cliente->bairro            = $request['bairro'];
            $cliente->cep               = Helper::removeMascara($request['cep']);
            $cliente->cidade            = $request['cidade'];
            $cliente->cod_mun           = $request['cidade'] !== null ? Cidade::where('nome',$cliente->cidade)->first()->cod_mun : null;
            $cliente->uf                = $request['uf'];
            $cliente->ddd               = Helper::removeMascara($request['ddd']);
            $cliente->fone              = Helper::removeMascara($request['fone']);
            $cliente->nome_contato      = $request['nome_contato'];
            $cliente->email             = $request['email'];
            $cliente->status            = isset($request['status']) ? $request['status'] : 0;
            $cliente->created_at        = new \DateTime();
            $cliente->updated_at        = new \DateTime();
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
                $cliente->loja              = null;
                $cliente->tipo_pessoa       = strtoupper($request['tipo_pessoa']);
                $cliente->razao_social      = $request['razao_social'];
                $cliente->nome_fantasia     = isset($request['nome_fantasia']) ? $request['nome_fantasia'] : $request['razao_social'];
                $cliente->cnpj_cpf          = $request['cnpj_cpf'];
                $cliente->tipo_cliente      = strtoupper($request['tipo_cliente']);
                $cliente->endereco          = $request['endereco'];
                $cliente->bairro            = $request['bairro'];
                $cliente->cep               = Helper::removeMascara($request['cep']);
                $cliente->cidade            = $request['cidade'];
                $cliente->cod_mun           = $request['cidade'] !== null ? Cidade::where('nome',$cliente->cidade)->first()->cod_mun : null;
                $cliente->uf                = $request['uf'];
                $cliente->ddd               = Helper::removeMascara($request['ddd']);
                $cliente->fone              = Helper::removeMascara($request['fone']);
                $cliente->nome_contato      = $request['nome_contato'];
                $cliente->email             = $request['email'];
                $cliente->status            = isset($request['status']) ? $request['status'] : 0;
                $cliente->updated_at        = new \DateTime();
                $cliente->save();


                //gera vex sync
                VexSyncController::adiciona(Helper::formataTenantId($this->empfilId), 'put', $cliente->getTable(), $cliente->id, $cliente->getWebservice('edit'));

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