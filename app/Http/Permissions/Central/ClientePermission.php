<?php 

namespace App\Http\Permissions\Central;

class ClientePermission
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

       'lista'         => [ 'codigo' => 'CENCLI001', 'prioridade' => '1', 'superior' => null,        'controle' => '1', 'titulo' => 'Cadastros > Clientes', 'descricao' => 'Tela de clientes'],
       'adiciona'      => [ 'codigo' => 'CENCLI002', 'prioridade' => '0', 'superior' => 'CENCLI001', 'controle' => '1', 'titulo' => 'Cadastros > Clientes', 'descricao' => 'Tela de cadastro de cliente'],
       'adicionaPost'  => [ 'codigo' => 'CENCLI003', 'prioridade' => '0', 'superior' => 'CENCLI001', 'controle' => '1', 'titulo' => 'Cadastros > Clientes', 'descricao' => 'Adicionar cliente'],
       'edita'         => [ 'codigo' => 'CENCLI004', 'prioridade' => '0', 'superior' => 'CENCLI001', 'controle' => '1', 'titulo' => 'Cadastros > Clientes', 'descricao' => 'Tela de edição de cliente'],
       'editaPost'     => [ 'codigo' => 'CENCLI005', 'prioridade' => '0', 'superior' => 'CENCLI001', 'controle' => '1', 'titulo' => 'Cadastros > Clientes', 'descricao' => 'Editar cliente'],
       'visualiza'     => [ 'codigo' => 'CENCLI006', 'prioridade' => '0', 'superior' => 'CENCLI001', 'controle' => '1', 'titulo' => 'Cadastros > Clientes', 'descricao' => 'Visualizar cliente'],
    ];

}