<?php 

namespace App\Http\Permissions\Central;

class ProdutoPermission
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

       'lista'         => [ 'codigo' => 'CENPRO001', 'prioridade' => '1', 'superior' => null,        'controle' => '1', 'titulo' => 'Cadastros > Produtos', 'descricao' => 'Tela de produtos'],
       'visualiza'     => [ 'codigo' => 'CENPRO002', 'prioridade' => '0', 'superior' => 'CENPRO001', 'controle' => '1', 'titulo' => 'Cadastros > Produtos', 'descricao' => 'Visualizar produto'],
    ];

}