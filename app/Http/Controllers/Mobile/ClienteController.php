<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Assinatura;
use App\Cliente;
use App\EmpresaFilial;
use App\Http\Controllers\Mobile\VexSyncController;

//mails

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//packages
use GuzzleHttp\Client;

//extras
use Validator; 
use Carbon\Carbon;
use App\Rules\Cpf;
use App\Rules\Cnpj;
use App\Utils\Aliases;
use App\Utils\Helper;

class ClienteController extends Controller
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


        $clientes = Cliente::where(function($query){
            if($this->filial !== null)
            {
                $query->where('vxgloempfil_id',$this->filial->id);
                $query->orWhere('vxgloempfil_id',null);
            }
        })->where('status','1')->orderBy('nome_fantasia','asc')->get();

        $response['success']  = $success;
        $response['log']      = $log;
        $response['clientes'] = $clientes;
        return $response;
    }

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

        if($success)
        {
            $cliente = new Cliente();
            $cliente->erp_id            = null;
            $cliente->loja              = null;
            $cliente->vxgloempfil_id    = $this->filial->id;
            $cliente->tipo_pessoa       = strtoupper($request['tipo_pessoa']);
            $cliente->razao_social      = $request['razao_social'];
            $cliente->nome_fantasia     = isset($request['nome_fantasia']) ? $request['nome_fantasia'] : $request['razao_social'];
            $cliente->cnpj_cpf          = $request['cnpj_cpf'];
            $cliente->tipo_cliente      = 'F';
            $cliente->endereco          = $request['endereco'];
            $cliente->bairro            = $request['bairro'];
            $cliente->complemento       = $request['complemento'];
            $cliente->cep               = $request['cep'];
            $cliente->cod_mun           = $request['cod_mun'];
            $cliente->cidade            = $request['cidade'];
            $cliente->uf                = $request['uf'];
            $cliente->ddd               = $request['ddd'];
            $cliente->fone              = $request['fone'];
            $cliente->nome_contato      = $request['nome_contato'];
            $cliente->email             = $request['email'];
            $cliente->status            = isset($request['status']) ? $request['status'] : 0;
            $cliente->created_at        = new \DateTime();
            $cliente->updated_at        = new \DateTime();
            $cliente->save();

            //gera vex sync
            VexSyncController::adiciona('99,01', 'post', $cliente->getTable(), $cliente->id, $cliente->getWebservice('add'));

            $log[]   = ['success' => 'Cliente cadastrado com sucesso'];

        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['cliente'] = isset($cliente) ? $cliente : null;
        return $response;
    }

    
    public function visualiza($cliente_id)
    {
        $success = true;
        $log     = [];

        $cliente = Cliente::find($cliente_id);

        $response['success'] = $success;
        $response['log']     = $log;
        $response['cliente'] = $cliente;
        return $response;
    }


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
                'cnpj_cpf'  => ['required','unique:vx_glo_cli,cnpj_cpf,'.$cliente_id.',id,deleted_at,NULL', $request['tipo_pessoa'] == 'F' ? new Cpf() : new Cnpj()],
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
    
            if($success)
            {
                $cliente->vxgloempfil_id    = $this->filial->id;
                $cliente->tipo_pessoa       = strtoupper($request['tipo_pessoa']);
                $cliente->razao_social      = $request['razao_social'];
                $cliente->nome_fantasia     = $request['nome_fantasia'];
                $cliente->cnpj_cpf          = $request['cnpj_cpf'];
                //$cliente->tipo_cliente      = strtoupper($request['tipo_cliente']); utilizado apenas em adicionaPost
                $cliente->endereco          = $request['endereco'];
                $cliente->bairro            = $request['bairro'];
                $cliente->complemento       = $request['complemento'];
                $cliente->cep               = $request['cep'];
                $cliente->cod_mun           = $request['cod_mun'];
                $cliente->cidade            = $request['cidade'];
                $cliente->uf                = $request['uf'];
                $cliente->ddd               = $request['ddd'];
                $cliente->fone              = $request['fone'];
                $cliente->nome_contato      = $request['nome_contato'];
                $cliente->email             = $request['email'];
                $cliente->status            = $request['status'];
                $cliente->updated_at        = new \DateTime();
                $cliente->save();
    
                //gera vex sync
                VexSyncController::adiciona('99,01', 'put', $cliente->getTable(), $cliente->id, $cliente->getWebservice('edit'));
    
                $log[]   = ['success' => 'Cliente atualizado com sucesso'];
    
            }
        }
        

        $response['success'] = $success;
        $response['log']     = $log;
        $response['cliente'] = isset($cliente) ? $cliente : null;
        return $response;
    }


    //post via vexsync
    public static function syncPost($sync)
    {
        $success = true;
        $log     = '';

        //busca dados da assinatura
        $assinatura = Assinatura::first();

        //busca item da tabela que será adiciona no ERP
        $object = DB::table($sync->tabela)->where('id', $sync->tabela_id)->first();

        //formata objeto para enviar no vexsync
        $object = Helper::formataSyncObject($object);

        //insere item no ERP
        try 
        {
            $guzzle  = new Client();
            $result  = $guzzle->request('POST', $assinatura->webservice_base . $sync->webservice, [
                'headers'     => [
                    'Content-Type'  => 'application/json',
                    'tenantId'      => $sync->tenant
                ], 
                'body' => json_encode($object)
            ]);
    
            $result  = json_decode($result->getBody());
    
            if($result->success == false)
            {
                $success = false;
                $log     = $result->log;
            }
            else 
            {
                //atualiza o registro com o erp_id
                $object = Helper::retornoERP($result->result);
                $object = json_decode($object);

                DB::table($sync->tabela)->where('id', $sync->tabela_id)->update([
                    'erp_id' => $object->erp_id,
                ]);

                $log = 'Sincronização realizada com sucesso';
            }
    
        }
        catch(\Exception $e)
        {
            $success = false;
            $log     = $e->getMessage();
        }
        
        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }

    //put via vexsync
    public static function syncPut($sync)
    {
        $success = true;
        $log     = '';

        //busca dados da assinatura
        $assinatura = Assinatura::first();

        //busca item da tabela que será adiciona no ERP
        $object = DB::table($sync->tabela)->where('id', $sync->tabela_id)->first();

        //formata objeto para enviar no vexsync
        $object = Helper::formataSyncObject($object);

        //insere item no ERP
        try 
        {
            $guzzle  = new Client();
            $result  = $guzzle->request('PUT', $assinatura->webservice_base . $sync->webservice, [
                'headers'     => [
                    'Content-Type'    => 'application/json',
                    'tenantId'        => $sync->tenant
                ], 
                'body' => json_encode($object)
            ]);

            $result  = json_decode($result->getBody());
    
            if($result->success == false)
            {
                $success = false;
                $log     = $result->log;
            }
            else 
            {
                $object = Helper::retornoERP($result->result);
                $object = json_decode($object);

                $log = 'Sincronização realizada com sucesso';
            }
        }
        catch(\Exception $e)
        {
            $success = false;
            $log     = $e->getMessage();
        }
        
        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }

}