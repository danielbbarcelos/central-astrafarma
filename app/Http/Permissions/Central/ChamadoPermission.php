<?php 

namespace App\Http\Permissions\Central;

class ChamadoPermission
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

       'lista'         => [ 'codigo' => 'CENCHA001', 'prioridade' => '1', 'superior' => null,        'controle' => '1', 'titulo' => 'Suporte > Chamados', 'descricao' => 'Tela de chamados'],
       'adicionaPost'  => [ 'codigo' => 'CENCHA003', 'prioridade' => '0', 'superior' => 'CENCHA001', 'controle' => '1', 'titulo' => 'Suporte > Chamados', 'descricao' => 'Adicionar chamado'],
       'visualiza'     => [ 'codigo' => 'CENCHA006', 'prioridade' => '0', 'superior' => 'CENCHA001', 'controle' => '1', 'titulo' => 'Suporte > Chamados', 'descricao' => 'Visualizar chamado'],
       'interagePost'  => [ 'codigo' => 'CENCHA007', 'prioridade' => '0', 'superior' => 'CENCHA001', 'controle' => '1', 'titulo' => 'Suporte > Chamados', 'descricao' => 'Interagir em chamado'],
    ];

}