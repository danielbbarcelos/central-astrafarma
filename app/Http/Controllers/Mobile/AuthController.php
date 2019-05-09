<?php 

namespace App\Http\Controllers\Mobile; 

//models and controllers
use App\Assinatura;
use App\Dispositivo;
use App\User;

//mails
use App\Mail\RecuperacaoSenha;

//framework
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

//packages
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth ;

//extras
use Carbon\Carbon;
use Validator;

class AuthController extends Controller
{

    //construct
    public function __construct()
    {
        //
    }


    //post para realizar login via API
    public function loginPost(Request $request){

        $success = true;
        $log     = [];
        $token   = null;


        /*
         * Verifica se o dispositivo está cadastrado
         *
         */
        $dispositivo = Dispositivo::where('device_id','=',$request['device_id'])->first();

        if(!isset($dispositivo))
        {
            $success = false;

            if(env('AUTO_DEVICE') == true)
            {
                $observacao  = 'Dispositivo cadastrado automaticamente, após tentativa de login com e-mail '.$request['email'].', em '.Carbon::now()->format('d/m/Y à\s H:i:s').'.';
                $observacao .= "\nEndereço IP: ".$request->ip();

                $dispositivo = new Dispositivo();
                $dispositivo->descricao  = 'Não identificado '.Carbon::now()->format('Y-m-d H:i:s');
                $dispositivo->observacao = $observacao;
                $dispositivo->device_id  = $request['device_id'];
                $dispositivo->status     = '0';
                $dispositivo->created_at = new \DateTime();
                $dispositivo->updated_at = new \DateTime();
                $dispositivo->save();

                $log[]   = ['error' => 'uuid pendente'];
            }
            else
            {
                $log[]   = ['error' => 'uuid incorreto'];
            }
        }

        if($success)
        {

            try
            {
                $credentials = $request->only('email', 'password');

                if (!$token = JWTAuth::attempt($credentials))
                {
                    $success = false;
                    $log[]   = ['error' => 'E-mail ou senha inválida'];
                    $token   = null;
                }
                else
                {

                    /*
                     * Verifica se usuário possui acesso ao aplicativo
                     *
                     */
                    $user = User::where('email','=',$request['email'])->first();

                    if((int)$user->mobile == 0)
                    {
                        $success = false;
                        $log[]   = ['error' => 'O usuário não possui acesso ao aplicativo'];
                    }
                    else
                    {
                        /*
                         * Verifica se assinatura está bloqueada ou se expirou
                         *
                         */
                        $assinatura = Assinatura::first();

                        if((int)$assinatura->status == 0)
                        {
                            $success = false;
                            $log[]   = ['error' => 'O acesso ao sistema está bloqueado'];
                        }
                        elseif(Carbon::now() > Carbon::createFromFormat('Y-m-d',$assinatura->data_final))
                        {
                            $expireAt = Carbon::createFromFormat('Y-m-d',$assinatura->data_final)->format('d/m/Y');

                            $success = false;
                            $log[]   = ['error' => 'O acesso ao sistema expirou em '.$expireAt];
                        }

                        /*
                         * verifica se User está inativo
                         *
                         */
                        if((int)$user->status == 0)
                        {
                            $success = false;
                            $log[]   = ['error' => 'Usuário inativo'];
                        }

                        /*
                         * Verifica se o dispositivo utilizado está ativo
                         *
                         */
                        if((int)$dispositivo->status == 0)
                        {
                            $success = false;
                            $log[]   = ['error' => 'O dispositivo está inativo'];
                        }

                    }

                }

            }
            catch (JWTException $e) {

                $success = false;
                $log[]   = ['error'=>'Ocorreu um erro'];
            }

        }


        $response['success']    = $success;
        $response['log']        = $log;
        $response['token']      = $success ? $token : null;
        return $response;
    }


}