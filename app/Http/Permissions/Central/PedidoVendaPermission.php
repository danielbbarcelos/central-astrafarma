<?php 

namespace App\Http\Permissions\Central;

class PedidoVendaPermission
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

       'lista'         => [ 'codigo' => 'CENPVE001', 'prioridade' => '1', 'superior' => null,        'controle' => '1', 'titulo' => 'Vendas > Pedidos', 'descricao' => 'Tela de pedidos de venda'],
       'adiciona'      => [ 'codigo' => 'CENPVE002', 'prioridade' => '0', 'superior' => 'CENPVE001', 'controle' => '1', 'titulo' => 'Vendas > Pedidos', 'descricao' => 'Tela de cadastro de pedido de venda'],
       'adicionaPost'  => [ 'codigo' => 'CENPVE003', 'prioridade' => '0', 'superior' => 'CENPVE001', 'controle' => '1', 'titulo' => 'Vendas > Pedidos', 'descricao' => 'Adicionar pedido de venda'],
       'novoPedido'    => [ 'codigo' => 'CENPVE103', 'prioridade' => '0', 'superior' => 'CENPVE001', 'controle' => '1', 'titulo' => 'Vendas > Pedidos', 'descricao' => 'Adicionar pedido de venda'],
       'edita'         => [ 'codigo' => 'CENPVE004', 'prioridade' => '0', 'superior' => 'CENPVE001', 'controle' => '1', 'titulo' => 'Vendas > Pedidos', 'descricao' => 'Tela de edição de pedido de venda'],
       'editaPost'     => [ 'codigo' => 'CENPVE005', 'prioridade' => '0', 'superior' => 'CENPVE001', 'controle' => '1', 'titulo' => 'Vendas > Pedidos', 'descricao' => 'Editar pedido de venda'],
       'visualiza'     => [ 'codigo' => 'CENPVE006', 'prioridade' => '0', 'superior' => 'CENPVE001', 'controle' => '1', 'titulo' => 'Vendas > Pedidos', 'descricao' => 'Visualizar pedido de venda'],
       'excluiPost'    => [ 'codigo' => 'CENPVE007', 'prioridade' => '0', 'superior' => 'CENPVE001', 'controle' => '1', 'titulo' => 'Vendas > Pedidos', 'descricao' => 'Excluir pedido de venda'],
       'imprimePDF'    => [ 'codigo' => 'CENPVE008', 'prioridade' => '0', 'superior' => 'CENPVE001', 'controle' => '1', 'titulo' => 'Vendas > Pedidos', 'descricao' => 'Gerar PDF de pedido de venda'],
    ];

}
