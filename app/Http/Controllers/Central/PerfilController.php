<?php 

namespace App\Http\Controllers\Central; 

//models and controllers
use App\Assinatura;
use App\EmpresaFilial;

//mails

//framework
use App\Http\Controllers\Controller;
use App\Perfil;
use App\PerfilPermissao;
use App\Permissao;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//packages

//extras
use Validator; 

class PerfilController extends Controller
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

        $perfis = Perfil::orderBy('nome','asc')->get();

        $response['success'] = $success;
        $response['log']     = $log;
        $response['perfis']   = $perfis;
        return $response;
    }


    //chamada da tela para adicionar um objeto
    public function adiciona()
    {
        $success = true;
        $log     = [];

        $perfil = new Perfil();

        $perfilPermissoes = [];

        /*
         * Busca as permissões cadastradas e separa as permissões por blocos de familia
         *
         */
        $itens = Permissao::where('controle','=','1')->orderBy('titulo','asc')->orderBy('id','asc')->get();

        $permissoes = [];

        foreach ($itens as $item)
        {
            $permissoes[$item->titulo][$item->locator][$item->function]['id']         = $item->id;
            $permissoes[$item->titulo][$item->locator][$item->function]['descricao']  = $item->descricao;
            $permissoes[$item->titulo][$item->locator][$item->function]['codigo']     = $item->codigo;
            $permissoes[$item->titulo][$item->locator][$item->function]['prioridade'] = $item->prioridade;
            $permissoes[$item->titulo][$item->locator][$item->function]['superior']   = $item->superior;
        }

        $response['success']           = $success;
        $response['log']               = $log;
        $response['perfil']            = $perfil;
        $response['perfilPermissoes']  = $perfilPermissoes;
        $response['permissoes']        = $permissoes;
        return $response;

    }


    //post para adicionar um objeto
    public function adicionaPost(Request $request)
    {
        $success = true;
        $log     = [];

        $rules   = [
            'nome'  => ['unique:vx_web_perfil,nome,NULL,id,deleted_at,NULL'],
        ];

        $validator = Validator::make($request->all(), $rules, Perfil::$messages);

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


            $perfil = new Perfil();
            $perfil->nome       = $request['nome'];
            $perfil->descricao  = $request['descricao'];
            $perfil->status     = $request['status'] ? '1' : '0';
            $perfil->created_at = new \DateTime();
            $perfil->updated_at = new \DateTime();
            $perfil->save();


            if(isset($request['perfil_permissoes']))
            {
                foreach ($request['perfil_permissoes'] as $permissao)
                {
                    $perfilPermissao = new PerfilPermissao();
                    $perfilPermissao->vxwebperfil_id = $perfil->id;
                    $perfilPermissao->vxwebpermis_id = $permissao;
                    $perfilPermissao->created_at     = new \DateTime();
                    $perfilPermissao->updated_at     = new \DateTime();
                    $perfilPermissao->save();
                }
            }

            $log[] = ['success' => 'Perfil de acesso cadastrado com sucesso'];
        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //chamada da tela para editar um objeto
    public function edita($perfil_id)
    {
        $success = true;
        $log     = [];

        $perfil = Perfil::find($perfil_id);

        if(!isset($perfil))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            $itens            = PerfilPermissao::select('vxwebpermis_id')->where('vxwebperfil_id','=',$perfil_id)->get();
            $perfilPermissoes = [];

            foreach($itens as $item)
            {
                $perfilPermissoes[] = $item->vxwebpermis_id;
            }


            /*
             * Busca as permissões cadastradas e separa as permissões por blocos de familia
             *
             */
            $itens = Permissao::where('controle','=','1')->orderBy('titulo','asc')->orderBy('id','asc')->get();

            $permissoes = [];

            foreach ($itens as $item)
            {
                $permissoes[$item->titulo][$item->locator][$item->function]['id']         = $item->id;
                $permissoes[$item->titulo][$item->locator][$item->function]['descricao']  = $item->descricao;
                $permissoes[$item->titulo][$item->locator][$item->function]['codigo']     = $item->codigo;
                $permissoes[$item->titulo][$item->locator][$item->function]['prioridade'] = $item->prioridade;
                $permissoes[$item->titulo][$item->locator][$item->function]['superior']   = $item->superior;
            }

        }

        $response['success']          = $success;
        $response['log']              = $log;
        $response['perfil']           = $perfil;
        $response['perfilPermissoes'] = isset($perfilPermissoes) ? $perfilPermissoes : [];
        $response['permissoes']       = isset($permissoes) ? $permissoes : [];
        return $response;

    }


    //post para editar um objeto
    public function editaPost(Request $request, $perfil_id)
    {
        $success = true;
        $log     = [];

        $perfil = Perfil::find($perfil_id);

        if(!isset($perfil))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            $rules = [
                'nome'  => ['unique:vx_web_perfil,nome,'.$perfil_id.',id,deleted_at,NULL'],
            ];

            $validator = Validator::make($request->all(), $rules, Perfil::$messages);

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
                $perfil->nome       = $request['nome'];
                $perfil->descricao  = $request['descricao'];
                $perfil->status     = $request['status'] ? '1' : '0';
                $perfil->updated_at = new \DateTime();
                $perfil->save();


                PerfilPermissao::where('vxwebperfil_id','=',$perfil_id)->delete();

                if(isset($request['perfil_permissoes']))
                {
                    foreach ($request['perfil_permissoes'] as $permissao)
                    {
                        $perfilPermissao = new PerfilPermissao();
                        $perfilPermissao->vxwebperfil_id = $perfil->id;
                        $perfilPermissao->vxwebpermis_id = $permissao;
                        $perfilPermissao->created_at     = new \DateTime();
                        $perfilPermissao->updated_at     = new \DateTime();
                        $perfilPermissao->save();
                    }
                }

                $log[] = ['success' => 'Perfil de acesso atualizado com sucesso'];

            }
        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


    //chamada da tela para visualizar um objeto
    public function visualiza($perfil_id)
    {
        $success = true;
        $log     = [];

        $perfil = Perfil::find($perfil_id);

        if(!isset($perfil))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            $itens            = PerfilPermissao::select('vxwebpermis_id')->where('vxwebperfil_id','=',$perfil_id)->get();
            $perfilPermissoes = [];

            foreach($itens as $item)
            {
                $perfilPermissoes[] = $item->vxwebpermis_id;
            }


            /*
             * Busca as permissões cadastradas e separa as permissões por blocos de familia
             *
             */
            $itens = Permissao::where('controle','=','1')->orderBy('titulo','asc')->orderBy('id','asc')->get();

            $permissoes = [];

            foreach ($itens as $item)
            {
                $permissoes[$item->titulo][$item->locator][$item->function]['id']         = $item->id;
                $permissoes[$item->titulo][$item->locator][$item->function]['descricao']  = $item->descricao;
                $permissoes[$item->titulo][$item->locator][$item->function]['codigo']     = $item->codigo;
                $permissoes[$item->titulo][$item->locator][$item->function]['prioridade'] = $item->prioridade;
                $permissoes[$item->titulo][$item->locator][$item->function]['superior']   = $item->superior;
            }

        }

        $response['success']          = $success;
        $response['log']              = $log;
        $response['perfil']           = $perfil;
        $response['perfilPermissoes'] = isset($perfilPermissoes) ? $perfilPermissoes : [];
        $response['permissoes']       = isset($permissoes) ? $permissoes : [];
        return $response;
    }


    //post para excluir um objeto
    public function excluiPost(Request $request, $perfil_id)
    {
        $perfil = Perfil::find($perfil_id);

        if(!isset($perfil))
        {
            $success = false;
            $log[]   = ['error' => 'Item não encontrado'];
        }
        else
        {
            if($perfil->nome == 'Administrador')
            {
                $success = false;
                $log[]   = ['error' => 'Não é possível excluir o perfil Administrador'];
            }
            else
            {
                $user = User::where('status','=','1')->where('vxwebperfil_id','=',$perfil_id)->get();

                if(count($user) > 0)
                {
                    $success = false;
                    $log[]   = ['error' => 'Não é possível excluir o perfil, pois ele está sendo usado por '.count($user).' usuário(s) ativo(s).'];
                }
            }

            if($success)
            {

                PerfilPermissao::where('vxwebperfil_id','=',$perfil_id)->delete();

                $perfil->delete();

                $log[] = ['success' => 'Perfil excluído com sucesso'];

            }
        }

        $response['success'] = $success;
        $response['log']     = $log;
        return $response;
    }


}