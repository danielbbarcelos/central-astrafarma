<?php 

namespace App\Http\Permissions\Central;

class UserPermission 
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

       'lista'            => [ 'codigo' => 'CENUSU001', 'prioridade' => '1', 'superior' => null,        'controle' => '1', 'titulo' => 'Sistema > Usuários', 'descricao' => 'Tela de usuários'],
       'adiciona'         => [ 'codigo' => 'CENUSU002', 'prioridade' => '0', 'superior' => 'CENUSU001', 'controle' => '1', 'titulo' => 'Sistema > Usuários', 'descricao' => 'Tela de cadastro de usuário'],
       'adicionaPost'     => [ 'codigo' => 'CENUSU003', 'prioridade' => '0', 'superior' => 'CENUSU001', 'controle' => '1', 'titulo' => 'Sistema > Usuários', 'descricao' => 'Adicionar usuário'],
       'edita'            => [ 'codigo' => 'CENUSU004', 'prioridade' => '0', 'superior' => 'CENUSU001', 'controle' => '1', 'titulo' => 'Sistema > Usuários', 'descricao' => 'Tela de edição de usuário'],
       'editaPost'        => [ 'codigo' => 'CENUSU005', 'prioridade' => '0', 'superior' => 'CENUSU001', 'controle' => '1', 'titulo' => 'Sistema > Usuários', 'descricao' => 'Editar usuário'],
       'visualiza'        => [ 'codigo' => 'CENUSU006', 'prioridade' => '0', 'superior' => 'CENUSU001', 'controle' => '1', 'titulo' => 'Sistema > Usuários', 'descricao' => 'Visualizar usuário'],
       'excluiPost'       => [ 'codigo' => 'CENUSU007', 'prioridade' => '0', 'superior' => 'CENUSU001', 'controle' => '1', 'titulo' => 'Sistema > Usuários', 'descricao' => 'Excluir usuário'],
       'configuracao'     => [ 'codigo' => 'CENUSU008', 'prioridade' => '0', 'superior' => 'CENUSU001', 'controle' => '1', 'titulo' => 'Sistema > Usuários', 'descricao' => 'Tela de configuração geral'],
       'configuracaoPost' => [ 'codigo' => 'CENUSU009', 'prioridade' => '0', 'superior' => 'CENUSU001', 'controle' => '1', 'titulo' => 'Sistema > Usuários', 'descricao' => 'Atualizar configurações'],
    ];

}