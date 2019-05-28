<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get ('/vex-sync/teste', function() {

    $object = new \stdClass();
    $object->tabela = 'vx_fat_tabprc';
    $object->ws = '/rest/vxfattabprc/008';
    return \App\Http\Controllers\Erp\VexSyncController::create($object);
});

Route::get ('/vex-sync/busca', 'Erp\VexSyncLocator@buscaPendencia');
Route::get ('/vex-sync/envia', 'Mobile\VexSyncLocator@sincroniza');

/*
* Autenticação
*
*/
Route::get ('/',                  'Central\AuthLocator@login');
Route::get ('/recuperacao-senha', 'Central\AuthLocator@recuperaSenha'); 
Route::get ('/logout',            'Central\AuthLocator@logout');

Route::group(['middleware' => 'throttle:6,1'], function() {

    Route::post('/',                  'Central\AuthLocator@loginPost');
    Route::post('/recuperacao-senha', 'Central\AuthLocator@recuperaSenhaPost'); //mensagem do Throttle disponível em: app/Exceptions/Handler.php -> render
});



Route::group(['middleware' => ['auth','status','session']], function() {

    /**
     * Home Dashboard
     *
     */
    Route::get ('/dashboard', 'Central\DashboardLocator@dashboard');

    /**
     * Alteração de filial
     *
     */
    Route::get ('/filial/{filial_id}', 'Central\DashboardLocator@selecionaFilial');

    /**
     * Alteração de senha
     *
     */
    Route::get ('/senha',     'Central\AuthLocator@alteraSenha');
    Route::post('/senha',     'Central\AuthLocator@alteraSenhaPost');

    /**
     * CRUD de clientes
     *
     */
    Route::get ('/clientes',                      'Central\ClienteLocator@lista');
    Route::get ('/clientes/add',                  'Central\ClienteLocator@adiciona');
    Route::post('/clientes/add',                  'Central\ClienteLocator@adicionaPost');
    Route::get ('/clientes/{cliente_id}/edit',    'Central\ClienteLocator@edita');
    Route::post('/clientes/{cliente_id}/edit',    'Central\ClienteLocator@editaPost');
    Route::get ('/clientes/{cliente_id}/show',    'Central\ClienteLocator@visualiza');

    /**
     *  CRUD de configurações gerais
     *
     */
    Route::get ('/configuracoes',             'Central\ConfiguracaoLocator@visualiza');
    Route::post('/configuracoes',             'Central\ConfiguracaoLocator@editaPost');

    /**
     * CRUD de dispositivos
     *
     */
    Route::get ('/dispositivos',                          'Central\DispositivoLocator@lista');
    Route::get ('/dispositivos/add',                      'Central\DispositivoLocator@adiciona');
    Route::post('/dispositivos/add',                      'Central\DispositivoLocator@adicionaPost');
    Route::get ('/dispositivos/{dispositivo_id}/edit',    'Central\DispositivoLocator@edita');
    Route::post('/dispositivos/{dispositivo_id}/edit',    'Central\DispositivoLocator@editaPost');
    Route::get ('/dispositivos/{dispositivo_id}/show',    'Central\DispositivoLocator@visualiza');
    Route::post('/dispositivos/{dispositivo_id}/del',     'Central\DispositivoLocator@excluiPost');


    /**
     * CRUD de pedidos
     *
     */
    Route::get ('/pedidos-vendas',                           'Central\PedidoVendaLocator@lista');
    Route::get ('/pedidos-vendas/add',                       'Central\PedidoVendaLocator@adiciona');
    Route::post('/pedidos-vendas/add',                       'Central\PedidoVendaLocator@adicionaPost');
    Route::get ('/pedidos-vendas/{pedido_venda_id}/edit',    'Central\PedidoVendaLocator@edita');
    Route::post('/pedidos-vendas/{pedido_venda_id}/edit',    'Central\PedidoVendaLocator@editaPost');
    Route::get ('/pedidos-vendas/{pedido_venda_id}/show',    'Central\PedidoVendaLocator@visualiza');
    Route::post('/pedidos-vendas/{pedido_venda_id}/del',     'Central\PedidoVendaLocator@excluiPost');
    Route::get ('/pedidos-vendas/{pedido_venda_id}/pdf',     'Central\PedidoVendaLocator@imprimePDF');


    /**
     * CRUD de produtos
     *
     */
    Route::get ('/produtos',                    'Central\ProdutoLocator@lista');
    Route::get ('/produtos/{produto_id}/show',  'Central\ProdutoLocator@visualiza');

    /**
     * CRUD de chamados
     *
     */
    Route::get ('/suporte/chamados',                       'Central\ChamadoLocator@lista');
    Route::post('/suporte/chamados/add',                   'Central\ChamadoLocator@adicionaPost');
    Route::get ('/suporte/chamados/{chamado_id}/show',     'Central\ChamadoLocator@visualiza');
    Route::post('/suporte/chamados/{chamado_id}/edit',     'Central\ChamadoLocator@interagePost');


    /**
     * CRUD de perfis
     *
     */
    Route::get ('/perfis',                     'Central\PerfilLocator@lista');
    Route::get ('/perfis/add',                 'Central\PerfilLocator@adiciona');
    Route::post('/perfis/add',                 'Central\PerfilLocator@adicionaPost');
    Route::get ('/perfis/{perfil_id}/edit',    'Central\PerfilLocator@edita');
    Route::post('/perfis/{perfil_id}/edit',    'Central\PerfilLocator@editaPost');
    Route::get ('/perfis/{perfil_id}/show',    'Central\PerfilLocator@visualiza');
    Route::post('/perfis/{perfil_id}/del',     'Central\PerfilLocator@excluiPost');


    /**
     * CRUD de usuários
     *
     */
    Route::get ('/usuarios',                         'Central\UserLocator@lista');
    Route::get ('/usuarios/add',                     'Central\UserLocator@adiciona');
    Route::post('/usuarios/add',                     'Central\UserLocator@adicionaPost');
    Route::get ('/usuarios/{user_id}/edit',          'Central\UserLocator@edita');
    Route::post('/usuarios/{user_id}/edit',          'Central\UserLocator@editaPost');
    Route::get ('/usuarios/{user_id}/show',          'Central\UserLocator@visualiza');
    Route::post('/usuarios/{user_id}/del',           'Central\UserLocator@excluiPost');
    Route::get ('/usuarios/{user_id}/configuracoes', 'Central\UserLocator@configuracao');
    Route::post('/usuarios/{user_id}/configuracoes', 'Central\UserLocator@configuracaoPost');

});



