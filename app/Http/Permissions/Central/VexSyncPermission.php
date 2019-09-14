<?php 

namespace App\Http\Permissions\Central;

class VexSyncPermission
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

       'lista'        => [ 'codigo' => 'CENSYN001', 'prioridade' => '1', 'superior' => null,        'controle' => '1', 'titulo' => 'Sistema > Logs de sincronização', 'descricao' => 'Tela de logs de sincronização'],
       'alteraStatus' => [ 'codigo' => 'CENSYN002', 'prioridade' => '0', 'superior' => 'CENSYN001', 'controle' => '1', 'titulo' => 'Sistema > Logs de sincronização', 'descricao' => 'Bloqueio e desbloqueio de sincronização'],
       'excluiPost'   => [ 'codigo' => 'CENSYN003', 'prioridade' => '0', 'superior' => 'CENSYN001', 'controle' => '1', 'titulo' => 'Sistema > Logs de sincronização', 'descricao' => 'Excluir log de sincronização'],

    ];

}