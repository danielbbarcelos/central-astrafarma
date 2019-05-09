<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
use App\Assinatura;
use App\Dispositivo;

//mails

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//packages

//extras
use Validator; 
use App\Rules\Mac;


class DispositivoController extends Controller
{

    //construct
    public function __construct()
    {
         //
    }


    //retorna array do objeto
    public function lista()
    {
        $success = true;
        $log     = [];

        $dispositivos = Dispositivo::orderBy('descricao','asc')->get();

        $response['success']      = $success;
        $response['log']          = $log;
        $response['dispositivos'] = $dispositivos;
        return $response;
    }


    //chamada da tela para adicionar um objeto
    public function adiciona()
    {
        $success = true;
        $log     = [];

        $dispositivo = new Dispositivo();

        $response['success'] = $success;
        $response['log']     = $log;
        $response['dispositivo'] = $dispositivo;
        return $response;
    }


    //post para adicionar um objeto
    public function adicionaPost(Request $request)
    {
        $success = true;
        $log     = [];

        $rules = [
            'descricao' => 'required',
            'device_id' => 'required|unique:vx_web_disp,device_id,NULL,id,deleted_at,NULL',
        ];

        $validator = Validator::make($request->all(), $rules, Dispositivo::$messages);

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
            $dispositivo = new Dispositivo();
            $dispositivo->descricao  = $request['descricao'];
            $dispositivo->observacao = $request['observacao'];
            $dispositivo->device_id  = $request['device_id'];
            $dispositivo->status     = isset($request['status']) ? '1' : '0';
            $dispositivo->created_at = new \DateTime();
            $dispositivo->updated_at = new \DateTime();
            $dispositivo->save();

            $log[] = ['success' => 'Dispositivo cadastrado com sucesso'];

            //caso o dispositivo seja cadastrado como ativo, é verificado se a quantidade de dispositivos ativos já foi ultrapassado
            if($dispositivo->status == '1')
            {
                $assinatura = Assinatura::first();

                $dispositivos = count(Dispositivo::select('id')->where('status','1')->get());

                if($dispositivos > (int) $assinatura->quantidade_dispositivo)
                {
                    $dispositivo->status = '0';
                    $dispositivo->save();

                    $log[] = ['error' => 'O status do dispositivo foi alterado para inativo, pois já existem '. $assinatura->quantidade_dispositivo. ' ativos cadastrados'];
                }

            }

        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //chamada da tela para editar um objeto
    public function edita($dispositivo_id)
    {
        $success = true;
        $log     = [];

        $dispositivo = Dispositivo::find($dispositivo_id);

        if(!isset($dispositivo))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['dispositivo'] = $dispositivo;
        return $response;
    }


    //post para editar um objeto
    public function editaPost(Request $request, $dispositivo_id)
    {
        $success = true;
        $log     = [];

        $dispositivo = Dispositivo::find($dispositivo_id);

        if(!isset($dispositivo))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            $rules = [
                'descricao' => 'required',
                'device_id' => 'required|unique:vx_web_disp,device_id,'.$dispositivo_id.',id,deleted_at,NULL',
            ];

            $validator = Validator::make($request->all(), $rules, Dispositivo::$messages);

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
                $dispositivo->descricao  = $request['descricao'];
                $dispositivo->observacao = $request['observacao'];
                $dispositivo->device_id  = $request['device_id'];
                $dispositivo->status     = isset($request['status']) ? '1' : '0';
                $dispositivo->updated_at = new \DateTime();
                $dispositivo->save();

                $log[] = ['success' => 'Dispositivo atualizado com sucesso'];

                //caso o dispositivo seja cadastrado como ativo, é verificado se a quantidade de dispositivos ativos já foi ultrapassado
                if($dispositivo->status == '1')
                {
                    $assinatura = Assinatura::first();

                    $dispositivos = count(Dispositivo::select('id')->where('status','1')->get());

                    if($dispositivos > (int) $assinatura->quantidade_dispositivo)
                    {
                        $dispositivo->status = '0';
                        $dispositivo->save();

                        $log[] = ['error' => 'O status do dispositivo foi alterado para inativo, pois já existem '. $assinatura->quantidade_dispositivo. ' ativos cadastrados'];
                    }

                }

            }

        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //chamada da tela para visualizar um objeto
    public function visualiza($dispositivo_id)
    {
        $success = true;
        $log     = [];

        $dispositivo = Dispositivo::find($dispositivo_id);

        if(!isset($dispositivo))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }

        $response['success'] = $success;
        $response['log']     = $log;
        $response['dispositivo'] = $dispositivo;
        return $response;
    }


    //post para excluir um objeto
    public function excluiPost(Request $request, $dispositivo_id)
    {
        $success = true;
        $log     = [];

        $dispositivo = Dispositivo::find($dispositivo_id);

        if(!isset($dispositivo))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            $dispositivo->delete();

            $log[] = ['success' => 'Dispositivo excluído com sucesso'];
        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


}