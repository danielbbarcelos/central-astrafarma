<?php 

namespace App\Http\Permissions\Central;

class CondicaoPagamentoPermission
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

       'lista' => [ 'codigo' => 'CENCPG001', 'prioridade' => '1', 'superior' => null, 'controle' => '1', 'titulo' => 'Cadastros > Condições de pagamento', 'descricao' => 'Tela de condições de pagamento'],
    ];

}