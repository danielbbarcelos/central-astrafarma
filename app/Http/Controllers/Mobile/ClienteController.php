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
use App\Vendedor;
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
    protected $vendedor;
    private static $logMessage = "Execução de VEX Sync em Mobile\ClienteController\n\n";

    //construct
    public function __construct($filialId = null, $user = null)
    {
        $this->filial = isset($filialId) ? EmpresaFilial::where('filial_erp_id',$filialId)->first() : null;
        $this->vendedor = isset($user->vxfatvend_id) ? Vendedor::find($user->vxfatvend_id) : null;
    }

    public function lista(Request $request)
    {
        $success = true;
        $log     = [];


        $clientes = Cliente::where(function($query){
            if($this->filial !== null)
            {
                $query->where('vxgloempfil_id',$this->filial->id);
                $query->orWhere('vxgloempfil_id',null);
            }
        })->where(function ($query) use ($request){

            if(isset($request['termo']))
            {
                $query->orWhereRaw('razao_social like "%'.$request['termo'].'%"');
                $query->orWhereRaw('nome_fantasia like "%'.$request['termo'].'%"');
                $query->orWhereRaw('cnpj_cpf like "'.$request['termo'].'"');
            }

        })->where(function($query){

            $query->where('vxfatvend_erp_id_1',$this->vendedor->erp_id);
            $query->orWhere('vxfatvend_erp_id_2',$this->vendedor->erp_id);

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
            $cliente->erp_id              = null;
            $cliente->vxgloempfil_id      = $this->filial->id;
            $cliente->vxfatvend_erp_id_2  = $this->vendedor->erp_id;
            $cliente->loja                = '01';
            $cliente->tipo_pessoa         = strtoupper($request['tipo_pessoa']);
            $cliente->razao_social        = $request['razao_social'];
            $cliente->nome_fantasia       = isset($request['nome_fantasia']) ? $request['nome_fantasia'] : $request['razao_social'];
            $cliente->cnpj_cpf            = $request['cnpj_cpf'];
            $cliente->contribuinte        = $request['contribuinte'];
            $cliente->insc_estadual       = (int)$cliente->contribuinte == 0 ? 'ISENTO' : Helper::formataString(strtoupper($request['insc_estadual']));
            $cliente->tipo_cliente        = 'F';
            $cliente->endereco            = $request['endereco'];
            $cliente->bairro              = $request['bairro'];
            $cliente->complemento         = $request['complemento'];
            $cliente->cep                 = $request['cep'];
            $cliente->cidade              = $request['cidade'];
            $cliente->cod_mun             = $request['cod_mun'];
            $cliente->uf                  = $request['uf'];
            $cliente->ddd                 = $request['ddd'];
            $cliente->fone                = $request['fone'];
            $cliente->nome_contato        = $request['nome_contato'];
            $cliente->email              = $request['email'];
            $cliente->email_con          = $request['email_con'];
            $cliente->email_fin          = $request['email_fin'];
            $cliente->envia_boleto        = $request['envia_boleto'];
            $cliente->obs_nota            = Helper::formataString($request['obs_nota'] !== null ? $request['obs_nota'] : '');
            $cliente->obs_interna         = Helper::formataString($request['obs_interna'] !== null ? $request['obs_interna'] : '');
            $cliente->risco               = 'E';
            $cliente->status              = '1';
            $cliente->created_at          = new \DateTime();
            $cliente->updated_at          = new \DateTime();
            $cliente->save();

            //gera vex sync
            VexSyncController::adiciona('01,01', 'post', $cliente->getTable(), $cliente->id, $cliente->getWebservice('add'));

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
                $cliente->vxgloempfil_id      = $this->filial->id;
                $cliente->vxfatvend_erp_id_2  = $this->vendedor->erp_id;
                $cliente->tipo_pessoa         = strtoupper($request['tipo_pessoa']);
                $cliente->razao_social        = $request['razao_social'];
                $cliente->nome_fantasia       = $request['nome_fantasia'];
                $cliente->cnpj_cpf            = $request['cnpj_cpf'];
                $cliente->contribuinte        = $request['contribuinte'];
                $cliente->insc_estadual       = (int)$cliente->contribuinte == 0 ? 'ISENTO' : Helper::formataString(strtoupper($request['insc_estadual']));
                //$cliente->tipo_cliente        = strtoupper($request['tipo_cliente']); utilizado apenas em adicionaPost
                $cliente->endereco            = $request['endereco'];
                $cliente->complemento         = $request['complemento'];
                $cliente->bairro              = $request['bairro'];
                $cliente->cep                 = $request['cep'];
                $cliente->cidade              = $request['cidade'];
                $cliente->cod_mun             = $request['cod_mun'];
                $cliente->uf                  = $request['uf'];
                $cliente->ddd                 = $request['ddd'];
                $cliente->fone                = $request['fone'];
                $cliente->nome_contato        = $request['nome_contato'];
                $cliente->email               = $request['email'];
                $cliente->email_con           = $request['email_con'];
                $cliente->email_fin           = $request['email_fin'];
                $cliente->envia_boleto        = $request['envia_boleto'];
                $cliente->obs_nota            = Helper::formataString($request['obs_nota'] !== null ? $request['obs_nota'] : '');
                $cliente->obs_interna         = Helper::formataString($request['obs_interna'] !== null ? $request['obs_interna'] : '');
                $cliente->updated_at          = new \DateTime();
                $cliente->save();
    
                //gera vex sync
                VexSyncController::adiciona('01,01', 'put', $cliente->getTable(), $cliente->id, $cliente->getWebservice('edit/'.$cliente->erp_id));
    
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
        $log     = self::$logMessage;

        //busca dados da assinatura
        $assinatura = Assinatura::first();

        //busca item da tabela que será adiciona no ERP
        $object = DB::table($sync->tabela)->where('id', $sync->tabela_id)->first();

        //formata objeto para enviar no vexsync
        $object = Helper::formataSyncObject($object);

        //adiciona ao log, objeto a ser enviado
        $log .= json_encode($object)."\n\n";

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
                $message = $result->log;
            }
            else 
            {
                //atualiza o registro com o erp_id
                $object = Helper::retornoERP($result->result);
                $object = json_decode($object);

                DB::table($sync->tabela)->where('id', $sync->tabela_id)->update([
                    'erp_id' => $object->erp_id,
                ]);

                $message = 'Sincronização realizada com sucesso';
            }

            $log .= $message;


        }
        catch(\Exception $e)
        {
            $success = false;
            $log     .= "Ocorreu um erro ao realizar o procedimento.\n\n";
            $log     .= 'Code '.$e->getFile().' - File: '.$e->getFile().' ('.$e->getLine().') - Message: '.$e->getMessage()."\n\n";
        }
        
        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }

    //put via vexsync
    public static function syncPut($sync)
    {
        $success = true;
        $log     = self::$logMessage;

        //busca dados da assinatura
        $assinatura = Assinatura::first();

        //busca item da tabela que será adiciona no ERP
        $object = DB::table($sync->tabela)->where('id', $sync->tabela_id)->first();

        //formata objeto para enviar no vexsync
        $object = Helper::formataSyncObject($object);

        //adiciona ao log, objeto a ser enviado
        $log .= json_encode($object)."\n\n";

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
                $message = $result->log;
            }
            else 
            {
                $object = Helper::retornoERP($result->result);
                $object = json_decode($object);

                $message = 'Sincronização realizada com sucesso';
            }

            $log .= $message;
        }
        catch(\Exception $e)
        {
            $success = false;
            $log     .= "Ocorreu um erro ao realizar o procedimento.\n\n";
            $log     .= 'Code '.$e->getFile().' - File: '.$e->getFile().' ('.$e->getLine().') - Message: '.$e->getMessage()."\n\n";
        }
        
        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }

}