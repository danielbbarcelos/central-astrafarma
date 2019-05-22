<?php

use Illuminate\Http\Request;
use App\Utils\Project;



Route::group(['prefix' => 'v1','middleware'=>[\Barryvdh\Cors\HandleCors::class]] , function(){

    //sincroniza banco de dados
    Route::get ('/vex-sync/sincroniza', 'Mobile\VexSyncLocator@sincroniza');
    Route::get ('/vex-sync/pendencias', 'Erp\VexSyncLocator@buscaPendencia');

    //retorna todas as funções do projeto
    Route::get('/project/functions', function(){
        return Project::getFunctions();
    });

    //retorna cidades através do estado selecionado
    Route::get ('/estados/{uf}/cidades', 'Central\EstadoLocator@buscaCidades');

    //login
    Route::post('/login', 'Mobile\AuthLocator@loginPost');

    /*
     * Auth::routes
     *
     */
    Route::group(['middleware'=>['jwt.auth','status']], function(){

        //armazens
        Route::get ('/armazens',              'Mobile\ArmazemLocator@lista');
        Route::get ('/armazem/{armazem_id}',  'Mobile\ArmazemLocator@visualiza');

        //clientes
        Route::get ('/clientes',                   'Mobile\ClienteLocator@lista');
        Route::post('/clientes/add',               'Mobile\ClienteLocator@adicionaPost');
        Route::get ('/clientes/{cliente_id}',      'Mobile\ClienteLocator@visualiza');
        Route::post('/clientes/{cliente_id}/edit', 'Mobile\ClienteLocator@editaPost');

        //condições de pagamento
        Route::get ('/condicoes',    'Mobile\CondicaoPagamentoLocator@lista');

        //empresas/filiais
        Route::get ('/empresas-filiais',                        'Mobile\EmpresaFilialLocator@lista');
        Route::get ('/empresas-filiais/{empresa_filial_id}',    'Mobile\EmpresaFilialLocator@visualiza');

        //pedidos
        Route::get ('/pedidos',                     'Mobile\PedidoVendaLocator@lista');
        Route::post('/pedidos/add',                 'Mobile\PedidoVendaLocator@adicionaPost');
        Route::get ('/pedidos/{pedido_id}',         'Mobile\PedidoVendaLocator@visualiza');
        Route::post('/pedidos/{pedido_id}/edit',    'Mobile\PedidoVendaLocator@editaPost');

        //produtos
        Route::get ('/produtos',                'Mobile\ProdutoLocator@lista');
        Route::get ('/produtos/{produto_id}',   'Mobile\ProdutoLocator@visualiza');

        //preços de produtos
        Route::get ('/precos-produtos/produto/{produto_id}',  'Mobile\PrecoProdutoLocator@listaPorProduto');
        Route::get ('/precos-produtos/{preco_produto_id}',    'Mobile\PrecoProdutoLocator@visualiza');

        //vendedores
        Route::get ('/vendedores',               'Mobile\VendedorLocator@lista');
        Route::get ('/vendedores/{vendedor_id}', 'Mobile\VendedorLocator@visualiza');

    });

});


