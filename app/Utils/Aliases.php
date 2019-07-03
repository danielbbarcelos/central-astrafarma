<?php

namespace App\Utils;

use App\Http\Controllers\Erp\ClienteController as ErpClienteController;
use App\Http\Controllers\Erp\CondicaoPagamentoController as ErpCondicaoPagamentoController;
use App\Http\Controllers\Erp\PedidoVendaController as ErpPedidoVendaController;
use App\Http\Controllers\Erp\TabelaPrecoController as ErpTabelaPrecoController;
use App\Http\Controllers\Erp\ProdutoController as ErpProdutoController;
use App\Http\Controllers\Erp\VendedorController as ErpVendedorController;
use App\Http\Controllers\Erp\VexSyncController as ErpVexSyncController;
use App\Http\Controllers\Mobile\ClienteController as MobileClienteController;
use App\Http\Controllers\Mobile\CondicaoPagamentoController as MobileCondicaoPagamentoController;
use App\Http\Controllers\Mobile\PedidoVendaController as MobilePedidoVendaController;
use App\Http\Controllers\Mobile\TabelaPrecoController as MobileTabelaPrecoController;
use App\Http\Controllers\Mobile\ProdutoController as MobileProdutoController;
use App\Http\Controllers\Mobile\VendedorController as MobileVendedorController;
use App\Http\Controllers\Mobile\VexSyncController as MobileVexSyncController;
use App\Http\Controllers\Migracao\ClienteController as MigracaoClienteController;
use App\Http\Controllers\Migracao\CondicaoPagamentoController as MigracaoCondicaoPagamentoController;
use App\Http\Controllers\Migracao\ProdutoController as MigracaoProdutoController;
use App\Http\Controllers\Migracao\TabelaPrecoController as MigracaoTabelaPrecoController;
use App\Http\Controllers\Migracao\VendedorController as MigracaoVendedorController;
use App\Http\Controllers\Migracao\ArmazemController as MigracaoArmazemController;
use App\Http\Controllers\Migracao\LoteController as MigracaoLoteController;

class Aliases
{
    //retorna o nome da controller erp, de acordo com o nome da tabela
    public static function erpControllerByTable($table)
    {
        $tables = [
            'vx_glo_cli'    => new ErpClienteController(),
            'vx_glo_cpgto'  => new ErpCondicaoPagamentoController(),
            'vx_fat_pvenda' => new ErpPedidoVendaController(),
            'vx_fat_tabprc' => new ErpTabelaPrecoController(),
            'vx_glo_prod'   => new ErpProdutoController(),
            'vx_fat_vend'   => new ErpVendedorController(),
            'vx_glo_sync'   => new ErpVexSyncController(),
        ];

        if(isset($tables[$table]))
        {
            return $tables[$table];
        }
        else 
        {
            return null;
        }
    }
    
    //retorna o nome da controller mobile, de acordo com o nome da tabela
    public static function mobileControllerByTable($table)
    {
        $tables = [
            'vx_glo_cli'    => new MobileClienteController(),
            'vx_glo_cpgto'  => new MobileCondicaoPagamentoController(),
            'vx_fat_pvenda' => new MobilePedidoVendaController(),
            'vx_fat_tabprc' => new MobileTabelaPrecoController(),
            'vx_glo_prod'   => new MobileProdutoController(),
            'vx_fat_vend'   => new MobileVendedorController(),
            'vx_glo_sync'   => new MobileVexSyncController(),
        ];

        if(isset($tables[$table]))
        {
            return $tables[$table];
        }
        else 
        {
            return null;
        }
    }


    //retorna o nome da controller de migração, de acordo com o nome da tabela
    public static function migracaoControllerByTable($table)
    {
        $tables = [
            'vx_glo_cli'    => new MigracaoClienteController(),
            'vx_glo_cpgto'  => new MigracaoCondicaoPagamentoController(),
            'vx_fat_tabprc' => new MigracaoTabelaPrecoController(),
            'vx_glo_prod'   => new MigracaoProdutoController(),
            'vx_fat_vend'   => new MigracaoVendedorController(),
            'vx_est_armz'   => new MigracaoArmazemController(),
            'vx_est_lote'   => new MigracaoLoteController(),
        ];

        if(isset($tables[$table]))
        {
            return $tables[$table];
        }
        else
        {
            return null;
        }
    }

    //retorna o nome entidade com base no nome da tabela
    public static function entityByTable($table)
    {
        $tables = [
            'vx_fat_ipvend' => 'Itens de pedidos de vendas',
            'vx_fat_pvenda' => 'Pedidos de vendas',
            'vx_fat_tabprc' => 'Tabelas de preços',
            'vx_fat_tpprod' => 'Produtos das tabelas de preços',
            'vx_fat_vend'   => 'Vendedores',
            'vx_glo_cidade' => 'Cadastro de cidades',
            'vx_glo_cli'    => 'Cliente',
            'vx_glo_cpgto'  => 'Condição de pagamento',
            'vx_glo_empfil' => 'Empresas e filiais',
            'vx_glo_estado' => 'Cadastro de estados',
            'vx_glo_prod'   => 'Produtos',
            'vx_glo_sync'   => 'VEX Sync',
            'vx_web_assina' => 'Assinatura da empresa',
            'vx_web_config' => 'Configurações gerais',
            'vx_web_disp'   => 'Dispositivos',
            'vx_web_perfil' => 'Perfis de acesso',
            'vx_web_permis' => 'Permissões do sistema',
            'vx_web_ppermi' => 'Permissões dos perfis de acesso',
            'vx_web_user'   => 'Usuários',
            'vx_web_userds' => 'Dashboards dos usuários',
            'vx_web_useref' => 'Filiais dos usuários',
        ];

        if(isset($tables[$table]))
        {
            return $tables[$table];
        }
        else
        {
            return $table;
        }
    }
}