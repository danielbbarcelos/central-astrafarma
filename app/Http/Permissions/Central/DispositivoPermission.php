<?php 

namespace App\Http\Permissions\Central;

class DispositivoPermission
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

       'lista'         => [ 'codigo' => 'CENDIS001', 'prioridade' => '1', 'superior' => null,        'controle' => '1', 'titulo' => 'Sistema > Dispositivos', 'descricao' => 'Tela de dispositivos'],
       'adiciona'      => [ 'codigo' => 'CENDIS002', 'prioridade' => '0', 'superior' => 'CENDIS001', 'controle' => '1', 'titulo' => 'Sistema > Dispositivos', 'descricao' => 'Tela de cadastro de dispositivo'],
       'adicionaPost'  => [ 'codigo' => 'CENDIS003', 'prioridade' => '0', 'superior' => 'CENDIS001', 'controle' => '1', 'titulo' => 'Sistema > Dispositivos', 'descricao' => 'Adicionar dispositivo'],
       'edita'         => [ 'codigo' => 'CENDIS004', 'prioridade' => '0', 'superior' => 'CENDIS001', 'controle' => '1', 'titulo' => 'Sistema > Dispositivos', 'descricao' => 'Tela de edição de dispositivo'],
       'editaPost'     => [ 'codigo' => 'CENDIS005', 'prioridade' => '0', 'superior' => 'CENDIS001', 'controle' => '1', 'titulo' => 'Sistema > Dispositivos', 'descricao' => 'Editar dispositivo'],
       'visualiza'     => [ 'codigo' => 'CENDIS006', 'prioridade' => '0', 'superior' => 'CENDIS001', 'controle' => '1', 'titulo' => 'Sistema > Dispositivos', 'descricao' => 'Visualizar dispositivo'],
       'excluiPost'    => [ 'codigo' => 'CENDIS007', 'prioridade' => '0', 'superior' => 'CENDIS001', 'controle' => '1', 'titulo' => 'Sistema > Dispositivos', 'descricao' => 'Excluir dispositivo'],
    ];

}