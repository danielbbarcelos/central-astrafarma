<?php 

namespace App\Http\Permissions\Central;

class PerfilPermission 
{

    /**
     * @var array
     * 
     * Array que contém as descrições, famílias e controle de permissões das funções do Locator,
     * 
     * O array é lido toda vez que as permissões forem atualizadas no banco de dados,
     * 
     * Para atualizar as funções disponíveis na tela de cadastro de perfil
     * 
     */ 
    public static $functions = [

       'lista'         => ['codigo' => 'CENPDA001', 'prioridade' => '1', 'superior' => null,        'controle' => '1', 'titulo' => 'Sistema > Perfis de acesso', 'descricao' => 'Tela de perfis de acesso'],
       'adiciona'      => ['codigo' => 'CENPDA002', 'prioridade' => '0', 'superior' => 'CENPDA001', 'controle' => '1', 'titulo' => 'Sistema > Perfis de acesso', 'descricao' => 'Tela de cadastro de perfis de acesso'],
       'adicionaPost'  => ['codigo' => 'CENPDA003', 'prioridade' => '0', 'superior' => 'CENPDA001', 'controle' => '1', 'titulo' => 'Sistema > Perfis de acesso', 'descricao' => 'Adicionar perfil de acesso'],
       'edita'         => ['codigo' => 'CENPDA004', 'prioridade' => '0', 'superior' => 'CENPDA001', 'controle' => '1', 'titulo' => 'Sistema > Perfis de acesso', 'descricao' => 'Tela de edição de perfil de acesso'],
       'editaPost'     => ['codigo' => 'CENPDA005', 'prioridade' => '0', 'superior' => 'CENPDA001', 'controle' => '1', 'titulo' => 'Sistema > Perfis de acesso', 'descricao' => 'Editar perfil de acesso'],
       'visualiza'     => ['codigo' => 'CENPDA006', 'prioridade' => '0', 'superior' => 'CENPDA001', 'controle' => '1', 'titulo' => 'Sistema > Perfis de acesso', 'descricao' => 'Visualizar perfil de acesso'],
       'excluiPost'    => ['codigo' => 'CENPDA007', 'prioridade' => '0', 'superior' => 'CENPDA001', 'controle' => '1', 'titulo' => 'Sistema > Perfis de acesso', 'descricao' => 'Excluir perfil de acesso'],
    ];

}