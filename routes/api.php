<?php

use Illuminate\Http\Request;
use App\Utils\Project;



Route::group(['prefix' => 'v1','middleware'=>[\Barryvdh\Cors\HandleCors::class]] , function(){

    //sincroniza banco de dados
    Route::get ('/vex-sync/sincroniza', 'Mobile\VexSyncLocator@sincroniza');

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
        Route::get ('/condicoes-pagamentos',    'Mobile\CondicaoPagamentoLocator@lista');

        //empresas/filiais
        Route::get ('/empresas-filiais',                        'Mobile\EmpresaFilialLocator@lista');
        Route::get ('/empresas-filiais/{empresa_filial_id}',    'Mobile\EmpresaFilialLocator@visualiza');

        //faturamento
        Route::get ('/faturamento/dashboard', 'Mobile\FaturamentoLocator@dashboard');

        //pedidos
        Route::get ('/pedidos',                     'Mobile\PedidoVendaLocator@lista');
        Route::post('/pedidos/add',                 'Mobile\PedidoVendaLocator@adicionaPost');
        Route::get ('/pedidos/{pedido_id}',         'Mobile\PedidoVendaLocator@visualiza');
        Route::post('/pedidos/{pedido_id}/edit',    'Mobile\PedidoVendaLocator@editaPost');

        //produtos
        Route::get ('/produtos',                   'Mobile\ProdutoLocator@lista');
        Route::get ('/produtos/{produto_id}',      'Mobile\ProdutoLocator@visualiza');
        Route::post('/produtos/{produto_id}/edit', 'Mobile\ProdutoLocator@editaPost');
        Route::post('/produtos/{produto_id}/del',  'Mobile\ProdutoLocator@excluiPost');

        //tabela de preços
        Route::get ('/tabelas-precos',                     'Mobile\TabelaPrecoLocator@lista');
        Route::get ('/tabelas-precos/{id}/{uf}/produtos',  'Mobile\TabelaPrecoLocator@visualiza');

        //vendedores
        Route::get ('/vendedores',               'Mobile\VendedorLocator@lista');
        Route::get ('/vendedores/{vendedor_id}', 'Mobile\VendedorLocator@visualiza');


    });

});


