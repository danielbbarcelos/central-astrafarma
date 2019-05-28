<?php 

namespace App\Http\Permissions\Central;

class ConfiguracaoPermission
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

       'visualiza'  => [ 'codigo' => 'CENCFG001', 'prioridade' => '1', 'superior' => null,        'controle' => '1', 'titulo' => 'Sistema > Configurações', 'descricao' => 'Tela de configurações gerais'],
       'editaPost'  => [ 'codigo' => 'CENCFG002', 'prioridade' => '0', 'superior' => 'CENCFG001', 'controle' => '1', 'titulo' => 'Sistema > Configurações', 'descricao' => 'Atualizar configurações gerais'],

    ];

}