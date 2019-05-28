<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
use App\Assinatura;
use App\EmpresaFilial;
use App\Perfil;
use App\User;

//mails

//framework
use App\Http\Controllers\Controller;
use App\UserDashboard;
use App\UserEmpresaFilial;
use App\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//packages

//extras
use Validator; 

class UserController extends Controller
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

        $users = User::orderBy('name','asc')->get();

        $response['success'] = $success;
        $response['log']     = $log;
        $response['users']   = $users;
        return $response;
    }


    //chamada da tela para adicionar um objeto
    public function adiciona()
    {
        $success = true;
        $log     = [];

        $user = new User();

        $perfis = Perfil::orderBy('nome')->get();

        //busca as filiais para vincular ao usuário
        $filiais = EmpresaFilial::all();

        $userFiliais = [];

        $vendedores = Vendedor::orderBy('nome')->get();


        $response['success'] = $success;
        $response['log']     = $log;
        $response['user']    = $user;
        $response['perfis']  = $perfis;
        $response['filiais'] = $filiais;
        $response['userFiliais'] = $userFiliais;
        $response['vendedores']  = $vendedores;
        return $response;
    }


    //post para adicionar um objeto
    public function adicionaPost(Request $request)
    {
        $success = true;
        $log     = [];

        $rules = [
            'name'      => 'required',
            'email'     => 'required|unique:vx_web_user,email,NULL,id,deleted_at,NULL',
            'password'  => 'required',
        ];

        $validator = Validator::make($request->all(), $rules, User::$messages);

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

            $user = new User();
            $user->vxwebperfil_id = $request['vxwebperfil_id'];
            $user->vxfatvend_id   = $request['vxfatvend_id'];
            $user->type           = 'B';
            $user->name           = $request['name'];
            $user->email          = $request['email'];
            $user->password       = bcrypt($request['password']);
            $user->web            = isset($request['web']) ? '1' : '0';
            $user->mobile         = isset($request['mobile']) ? '1' : '0';
            $user->status         = isset($request['status']) ? '1' : '0';
            $user->created_at     = new \DateTime();
            $user->updated_at     = new \DateTime();
            $user->save();

            if(isset($request['empfil']))
            {
                foreach($request['empfil'] as $empfil)
                {
                    $userEmpfil = new UserEmpresaFilial();
                    $userEmpfil->vxwebuser_id   = $user->id;
                    $userEmpfil->vxgloempfil_id = $empfil;
                    $userEmpfil->created_at     = new \DateTime();
                    $userEmpfil->updated_at     = new \DateTime();
                    $userEmpfil->save();
                }
            }


            //caso o usuário seja cadastrado com acesso web, é verificado se a quantidade de usuários ativos com web já foi ultrapassado
            if($user->web == '1')
            {
                $assinatura = Assinatura::first();

                $users = count(User::select('id')->where('status','1')->where('web','1')->get());

                if($users > (int) $assinatura->quantidade_web_user)
                {
                    $user->web = '0';
                    $user->save();

                    $log[] = ['error' => 'O acesso web foi bloqueado, pois já existem '. $assinatura->quantidade_web_user. ' usuários ativos cadastrados com acesso web'];
                }
            }

            $log[] = ['success' => 'Usuário cadastrado com sucesso'];
        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //chamada da tela para editar um objeto
    public function edita($user_id)
    {
        $success = true;
        $log     = [];

        $user = User::find($user_id);

        if(!isset($user))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }

        $perfis = Perfil::orderBy('nome')->get();

        $filiais = EmpresaFilial::all();

        $userFiliais = [];

        foreach(UserEmpresaFilial::all() as $item)
        {
            $userFiliais[] = $item->vxgloempfil_id;
        }

        $vendedores = Vendedor::orderBy('nome')->get();

        $response['success'] = $success;
        $response['log']     = $log;
        $response['user']    = $user;
        $response['perfis']  = $perfis;
        $response['filiais'] = $filiais;
        $response['userFiliais'] = $userFiliais;
        $response['vendedores']  = $vendedores;
        return $response;
    }


    //post para editar um objeto
    public function editaPost(Request $request, $user_id)
    {
        $success = true;
        $log     = [];

        $user = User::find($user_id);

        if(!isset($user))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            $rules = [
                'email' => 'required|email|unique:vx_web_user,email,'.$user->id.',id,deleted_at,NULL',
            ];

            $validator = Validator::make($request->all(), $rules, User::$messages);

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
                //verifica qual é o status de acesso web antigo, para verificar se assinatura permite novo usuário web ativo
                $webAntigo = $user->web;

                if($user->type == 'A')
                {
                    $user->vxfatvend_id   = $request['vxfatvend_id'];
                    $user->email          = $request['email'];
                    $user->updated_at     = new \DateTime();
                    $user->save();
                }
                else
                {
                    $user->vxwebperfil_id = $request['vxwebperfil_id'];
                    $user->vxfatvend_id   = $request['vxfatvend_id'];
                    $user->name           = $request['name'];
                    $user->email          = $request['email'];
                    $user->web            = isset($request['web']) ? '1' : '0';
                    $user->mobile         = isset($request['mobile']) ? '1' : '0';
                    $user->status         = isset($request['status']) ? '1' : '0';
                    $user->updated_at     = new \DateTime();
                    $user->save();


                    if(isset($request['empfil']))
                    {
                        $empfils = UserEmpresaFilial::whereNotIn('vxgloempfil_id',$request['empfil'])->get();

                        foreach($empfils as $empfil)
                        {
                            if($empfil->id == $user->vxwebuseref_id)
                            {
                                $user->vxwebuseref_id = null;
                                $user->updated_at     = new \DateTime();
                                $user->save();
                            }

                            $empfil->forceDelete();
                        }


                        foreach($request['empfil'] as $empfil)
                        {
                            $userEmpfil = UserEmpresaFilial::where('vxgloempfil_id',$empfil)->first();

                            if(!isset($userEmpfil))
                            {
                                $userEmpfil = new UserEmpresaFilial();
                                $userEmpfil->vxwebuser_id   = $user->id;
                                $userEmpfil->vxgloempfil_id = $empfil;
                                $userEmpfil->created_at     = new \DateTime();
                                $userEmpfil->updated_at     = new \DateTime();
                                $userEmpfil->save();
                            }
                        }
                    }

                    //caso o usuário seja cadastrado com acesso web, é verificado se a quantidade de usuários ativos com web já foi ultrapassado
                    if($webAntigo == '0' and $user->web == '1')
                    {
                        $assinatura = Assinatura::first();

                        $users = count(User::select('id')->where('status','1')->where('web','1')->get());

                        if($users > (int) $assinatura->quantidade_web_user)
                        {
                            $user->web = '0';
                            $user->save();

                            $log[] = ['error' => 'O acesso web foi bloqueado, pois já existem '. $assinatura->quantidade_web_user. ' usuários ativos cadastrados com acesso web'];
                        }
                    }
                }

                $log[] = ['success' => 'Usuário atualizado com sucesso'];

            }
        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //chamada da tela para visualizar um objeto
    public function visualiza($user_id)
    {
        $success = true;
        $log     = [];

        $user = User::find($user_id);

        if(!isset($user))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }

        $perfis = Perfil::orderBy('nome')->get();

        $filiais = EmpresaFilial::all();

        $userFiliais = [];

        foreach(UserEmpresaFilial::all() as $item)
        {
            $userFiliais[] = $item->vxgloempfil_id;
        }


        $vendedores = Vendedor::orderBy('nome')->get();

        $response['success'] = $success;
        $response['log']     = $log;
        $response['user']    = $user;
        $response['perfis']  = $perfis;
        $response['filiais'] = $filiais;
        $response['userFiliais'] = $userFiliais;
        $response['vendedores']  = $vendedores;
        return $response;
    }


    //post para excluir um objeto
    public function excluiPost(Request $request, $user_id)
    {
        $success = true;
        $log     = [];

        $user = User::find($user_id);

        if(!isset($user))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            if($user->id == Auth::user()->id)
            {
                $success = false;
                $log[]   = ['error' => 'Não é possível excluir seu próprio usuário'];
            }
            elseif($user->type == 'A')
            {
                $success = false;
                $log[]   = ['error' => 'Não é possível excluir o usuário administrador'];
            }
            else 
            {
                $user->delete();

                //exclui as filiais vinculadas ao usuário
                UserEmpresaFilial::where('vxwebuser_id',$user_id)->delete();

                $log[]   = ['success' => 'Usuário excluído com sucesso'];
            }
        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }



    //chamada da tela para configurar itens gerais do usuário
    public function configuracao($user_id)
    {
        $success = true;
        $log     = [];

        $user = User::find($user_id);

        if(!isset($user))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            $dashboard = UserDashboard::where('vxwebuser_id',$user_id)->first();

            if(!isset($dashboard))
            {
                $dashboard = new UserDashboard();
                $dashboard->vxwebuser_id   = $user_id;
                $dashboard->assinatura_status = '0';
                $dashboard->bi_status      = '0';
                $dashboard->bi_url         = null;
                $dashboard->created_at     = new \DateTime();
                $dashboard->updated_at     = new \DateTime();
                $dashboard->save();
            }
        }

        $response['success']   = $success;
        $response['log']       = $log;
        $response['user']      = $user;
        $response['dashboard'] = isset($dashboard) ? $dashboard : null;
        return $response;
    }

    //post para configurar pedidos de venda
    public function configuracaoPost(Request $request, $user_id)
    {
        $success = true;
        $log     = [];


        $user = User::find($user_id);

        if(!isset($user))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            $dashboard = UserDashboard::where('vxwebuser_id',$user_id)->first();
            $dashboard->assinatura_status = isset($request['assinatura_status']) ? '1' : '0';
            $dashboard->bi_status         = isset($request['bi_status']) ? '1' : '0';
            $dashboard->bi_url            = $request['bi_url'];
            $dashboard->updated_at        = new \DateTime();
            $dashboard->save();
        }

        $log[] = ['success' => 'Configurações atualizadas com sucesso'];

        $response['success']      = $success;
        $response['log']          = $log;
        return $response;
    }



}