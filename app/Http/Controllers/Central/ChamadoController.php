<?php
namespace App\Http\Controllers\Central;

//models and controllers
use App\Assinatura;
use App\Chamado;
use App\ChamadoInteracao;

//mails and notifications
use App\Mail\ChamadoInteracao as ChamadoInteracaoMail;
use App\Notifications\Slack;

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

//packages
use GuzzleHttp\Client;

//extras
use Validator;


class ChamadoController extends Controller
{
    use Notifiable;

    private $assinatura;
    private $user;

    public function __construct()
    {
        $this->assinatura = Assinatura::first();
        $this->user       = Auth::user();
    }

    //Rota de notificação do slack
    public function routeNotificationForSlack()
    {
        return 'https://hooks.slack.com/services/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';
    }
    

    //lista todos os chamados
    public function lista($status = null)
    {
        $success = true;
        $log     = [];


        $title      = 'Todos os chamados';
        $chamados   = [];
        $codStatus  = null;

        //Verifica se a lista será filtrada por status, para gerar o title da pagina a ser aberta
        if(isset($status))
        {
            if($status == 'abertos')
            {
                $title      = 'Chamados em aberto';
                $codStatus  = 'A';
            }
            elseif($status == 'atendimento')
            {
                $title      = 'Chamados em atendimento';
                $codStatus  = 'E';
            }
            else
            {
                $title      = 'Chamados concluídos';
                $codStatus  = 'C';
            }
        }

        //busca os chamados cadastrados no vex admin
        $guzzle  = new Client();
        $result  = $guzzle->request('POST', env('ADMIN_URL') . '/api/v1/chamados', [
            'headers'     => [
                'Content-Type'    => 'application/json',
            ], 
            'body' => json_encode(['api_key' => $this->assinatura->api_key])
        ]);

        $result  = json_decode($result->getBody());

        if(!$result->success)
        {
            $success = $result->success;
            $log     = $result->log;
        }
        else 
        {
            $chamados = $result->chamados;
        }

        $response['success']  = $success;
        $response['log']      = $log;
        $response['chamados'] = $chamados;
        $response['title']    = $title;
        return $response;
    }


    //post para criar um chamado
    public function adicionaPost(Request $request)
    {
        $success = true;
        $log     = [];

        $rules = [
            'tipo'             => 'required',
            'assunto'          => 'required|max:200',
            'mensagem'         => 'required',
        ];

        $validator = Validator::make($request->all(), $rules, Chamado::$messages);

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

            //busca os chamados cadastrados no vex admin
            $guzzle  = new Client();

            $result  = $guzzle->request('POST', env('ADMIN_URL') . '/api/v1/chamados/add', [
                'headers'     => [
                    'Accept'  => 'application/json',
                ],
                'multipart' => [
                    [
                        'name'     => 'api_key',
                        'contents' => $this->assinatura->api_key,
                    ],
                    [
                        'name'     => 'tipo',
                        'contents' => $request['tipo'],
                    ],
                    [
                        'name'     => 'empresa_user_id',
                        'contents' => $this->user->id,
                    ],
                    [
                        'name'     => 'responsavel',
                        'contents' => json_encode(['id' => $this->user->id, 'name'=> $this->user->name, 'email' => $this->user->email], JSON_UNESCAPED_UNICODE),
                    ],
                    [
                        'name'     => 'assunto',
                        'contents' => $request['assunto'],
                    ],
                    [
                        'name'     => 'mensagem',
                        'contents' => $request['mensagem'],
                    ],
                    [
                        'name'     => 'upload',
                        'contents' => isset($request['upload']) ? fopen($request->file('upload')->getRealPath(),'r') : null,
                        'filename' => isset($request['upload']) ? 'chamado.zip' : null
                    ],
                ]
            ]);

            $result  = json_decode($result->getBody());

            if(!$result->success)
            {
                $success = $result->success;
                $log     = $result->log;
            }
            else 
            {
                $chamado = $result->chamado;
            }
            
        }
        
        $response['success'] = $success;
        $response['log']     = $log;
        $response['chamado'] = isset($chamado) ? $chamado : null;
        return $response;
    }


    //chamada para visualizar o chamado
    public function visualiza($chamado_id)
    {
        $success = true;
        $log     = [];
        $chamado = null;
        $chamadoInteracoes = [];


        //busca os chamados cadastrados no vex admin
        $guzzle  = new Client();
        $result  = $guzzle->request('POST', env('ADMIN_URL') . '/api/v1/chamados/'.$chamado_id.'/show', [
            'headers'     => [
                'Content-Type' => 'application/json',
            ], 
            'body' => json_encode(['api_key' => $this->assinatura->api_key])
        ]);

        $result  = json_decode($result->getBody());


        if(!$result->success)
        {
            $success = $result->success;
            $log     = $result->log;
        }
        else 
        {
            $chamado = $result->chamado;
            $chamadoInteracoes = $result->chamadoInteracoes;
        }

        $response['success']            = $success;
        $response['log']                = $log;
        $response['chamado']            = $chamado;
        $response['chamadoInteracoes']  = $chamadoInteracoes;
        return $response;
    }


    //post para realizar interação no ticket
    public function interagePost(Request $request, $chamado_id)
    {
        $success = true;
        $log     = [];


        $rules = [
            'acao'             => 'required',
            'mensagem'         => 'required',
        ];

        $validator = Validator::make($request->all(), $rules, Chamado::$messages);

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
            //busca os chamados cadastrados no vex admin
            $guzzle  = new Client();
            $result  = $guzzle->request('POST', env('ADMIN_URL') . '/api/v1/chamados/'.$chamado_id.'/edit', [
                'headers'     => [
                    'Accept'  => 'application/json',
                ], 
                'multipart' => [
                    [
                        'name'     => 'api_key',
                        'contents' => $this->assinatura->api_key,
                    ],
                    [
                        'name'     => 'acao',
                        'contents' => $request['acao'],
                    ],
                    [
                        'name'     => 'empresa_user_id',
                        'contents' => $this->user->id,
                    ],
                    [
                        'name'     => 'responsavel',
                        'contents' => json_encode(['id' => $this->user->id, 'name'=> $this->user->name, 'email' => $this->user->email], JSON_UNESCAPED_UNICODE),
                    ],
                    [
                        'name'     => 'mensagem',
                        'contents' => $request['mensagem'],
                    ],
                    [
                        'name'     => 'upload',
                        'contents' => isset($request['upload']) ? file_get_contents($request->file('upload')->getRealPath()) : null,
                        'filename' => isset($request['upload']) ? 'interacao.zip' : null
                    ],
                ]
            ]);

            $result  = json_decode($result->getBody());

            if(!$result->success)
            {
                $success = $result->success;
                $log     = $result->log;
            }
            
        }
        
        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }
}
