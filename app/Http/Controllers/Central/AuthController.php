<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
use App\Assinatura;
use App\Dispositivo;
use App\EmpresaFilial;
use App\Perfil;
use App\PerfilPermissao;
use App\Permissao;
use App\User;

//mails
use App\Mail\RecuperacaoSenha;

//framework
use App\Http\Controllers\Controller;
use App\UserEmpresaFilial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

//packages
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth ;

//extras
use Validator;
use Carbon\Carbon;



class AuthController extends Controller
{

    //construct
    public function __construct()
    {
        //
    }


    //chamada da tela para realizar login
    public function login()
    {
        $success = true;
        $log     = [];

        //

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //post para realizar login
    public function loginPost(Request $request)
    {
        $success = true;
        $log     = [];


        /*
         * Busca os dados do User para realizar a autenticação
         *
         */
        $user = User::where('email','=',$request['email'])->first();

        if(!$user)
        {
            $success = false;
            $log[]   = ['error' => 'Usuário não encontrado'];
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
                $log[]   = ['error' => 'O acesso ao sistema está bloqueado. Em caso de dúvidas, contatar a equipe Vex Mobile'];
            }
            elseif(Carbon::now() > Carbon::createFromFormat('Y-m-d',$assinatura->data_final))
            {
                $expireAt = Carbon::createFromFormat('Y-m-d',$assinatura->data_final)->format('d/m/Y');

                $success = false;
                $log[]   = ['error' => 'O acesso ao sistema expirou em '.$expireAt.'. Em caso de dúvidas, contatar a equipe Vex Mobile'];
            }

            /*
             * verifica se User está inativo
             *
             */
            if((int)$user->status == 0)
            {
                $success = false;
                $log[]   = ['error' => 'Usuário inativo. Em caso de dúvidas, entre em contato com a equipe de suporte'];
            }

            /*
             * verifica se o usuário possui acesso à web
             *
             */
            if((int)$user->web == 0)
            {
                $success = false;
                $log[]   = ['error' => 'O usuário não possui acesso ao sistema web. Por favor, entre em contato com a equipe de suporte'];
            }

        }

        /*
         *  Se não ocorrer nenhum erro, tentará fazer o login
         *
         */
        if($success)
        {
            $credentials    = $request->only(['email', 'password']);
            $remember       = $request->has('remember');

            if(!Auth::attempt($credentials, $remember))
            {
                $success = false;
                $log[]   = ['error' => 'Senha incorreta'];
            }
            else
            {
                //salva o token de autenticação para bloquear acesso do mesmo usuários em dois computadores
                $user->auth_token = md5($request->ip().' at '.Carbon::now()->format('Y-m-d H:i:s')) . str_random(50);
                $user->save();

                session(['auth.token' => $user->auth_token]);



                $perfil = Perfil::find($user->vxwebperfil_id);

                //caso seja o primeiro acesso, executamos o db:seed para carregar as permissões
                if(!isset($perfil))
                {
                    Artisan::call('db:seed', [
                        '--force'   => true
                    ]);
                }


                //Busca as permissões do User para gravar em Cache os acessos permitidos
                $permissoes = PerfilPermissao::where('vxwebperfil_id','=',$user->vxwebperfil_id)->get();
                $functions  = [];

                foreach($permissoes as $item)
                {
                    $permissao = Permissao::find($item->vxwebpermis_id);

                    if(isset($permissao))
                    {
                        $functions[] = $permissao->path.'\\'.$permissao->locator.'@'.$permissao->function;
                    }
                }

                $key = base64_encode(DB::connection()->getDatabaseName()).'#'.Auth::user()->id.'-permissions';

                Cache::forever($key, $functions);


                //caso o usuário não tenha uma filial selecionada, selecionamos a primeira filial cadastrada
                if($user->vxwebuseref_id == null)
                {
                    $userEmpfil = UserEmpresaFilial::where('vxwebuser_id',$user->id)->first();

                    if(!isset($userEmpfil))
                    {
                        $success = false;
                        $log[]   = ['error' => 'O seu usuário não está vinculado a nenhuma filial. Por favor, acione o administrador do seu sistema'];

                        Auth::logout();
                    }
                    else
                    {
                        $user->vxwebuseref_id = $userEmpfil->id;
                        $user->save();
                    }

                }
            }

        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }



    //chamada para realizar logout
    public function logout()
    {
        $success = true;
        $log     = [];

        Auth::logout();

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //chamada da tela para recuperar senha
    public function recuperaSenha()
    {
        $success = true;
        $log     = [];

        //

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //post para recuperação de senha
    public function recuperaSenhaPost(Request $request)
    {
        $success = true;
        $log     = [];

        /*
         *  Verifica se email digitado pertence a um usuário
         *
         */
        $user = User::select('id','email','status','name')->where('email','=',$request['email'])->first();

        if(!$user)
        {
            $success = false;
            $log[]   = ['error' => 'E-mail não encontrado'];
        }
        else
        {
            if((int)$user->status == 0)
            {
                $success = false;
                $log[]   = ['error' => 'O usuário encontrado está inativo'];
            }
        }

        /*
         *  Se não ocorrer nenhum erro, é gerada uma nova senha
         *
         */
        if($success)
        {
            $password = str_random(8);

            try
            {
                Mail::to($request['email'])->send(new RecuperacaoSenha($user, $password));

                $user->password      = bcrypt($password);
                $user->updated_at    = new \DateTime();
                $user->save();

                $log[] = ['success' => 'E-mail enviado com sucesso! Acesse seu e-mail e verifique a nova senha'];
            }
            catch(\Exception $mailException)
            {
                $success = false;
                $log[]   = ['error' => 'Não possível enviar o e-mail. Confira se o e-mail está correto ou tente novamente mais tarde'];
            }

        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }




    //chamada da tela para alterar a senha
    public function alteraSenha()
    {
        $success = true;
        $log     = [];

        //

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }



    //post para alterar a senha
    public function alteraSenhaPost(Request $request)
    {
        $success = true;
        $log     = [];

        if($request['password'] !== $request['confirm_password'])
        {
            $success = false;
            $log[]   = ['error' => 'A senha digitada não foi confirmada'];
        }
        else
        {
            $user = Auth::user();
            $user->password = bcrypt($request['password']);
            $user->save();

            $log[]   = ['success' => 'Alteração de senha realizada com sucesso'];
        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


}