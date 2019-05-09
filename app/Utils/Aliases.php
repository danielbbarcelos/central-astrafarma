<?php

namespace App\Utils;

use App\Http\Controllers\Erp\ArmazemController as ErpArmazemController;
use App\Http\Controllers\Erp\ClienteController as ErpClienteController;
use App\Http\Controllers\Erp\CondicaoPagamentoController as ErpCondicaoPagamentoController;
use App\Http\Controllers\Erp\PedidoVendaController as ErpPedidoVendaController;
use App\Http\Controllers\Erp\PrecoProdutoController as ErpPrecoProdutoController;
use App\Http\Controllers\Erp\ProdutoController as ErpProdutoController;
use App\Http\Controllers\Erp\VendedorController as ErpVendedorController;
use App\Http\Controllers\Erp\VexSyncController as ErpVexSyncController;
use App\Http\Controllers\Mobile\ArmazemController as MobileArmazemController;
use App\Http\Controllers\Mobile\ClienteController as MobileClienteController;
use App\Http\Controllers\Mobile\CondicaoPagamentoController as MobileCondicaoPagamentoController;
use App\Http\Controllers\Mobile\PedidoVendaController as MobilePedidoVendaController;
use App\Http\Controllers\Mobile\PrecoProdutoController as MobilePrecoProdutoController;
use App\Http\Controllers\Mobile\ProdutoController as MobileProdutoController;
use App\Http\Controllers\Mobile\VendedorController as MobileVendedorController;
use App\Http\Controllers\Mobile\VexSyncController as MobileVexSyncController;

class Aliases
{
    //retorna o nome da controller erp, de acordo com o nome da tabela
    public static function erpControllerByTable($table)
    {
        $tables = [
            //'armazens'      => new ErpArmazemController(),
            'vx_glo_cli'    => new ErpClienteController(),
            'vx_glo_cpgto'  => new ErpCondicaoPagamentoController(),
            'vx_fat_pvenda' => new ErpPedidoVendaController(),
            'vx_fat_tabprc' => new ErpPrecoProdutoController(),
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
            'armazens'      => new MobileArmazemController(),
            'vx_glo_cli'    => new MobileClienteController(),
            'vx_glo_cpgto'  => new MobileCondicaoPagamentoController(),
            'vx_fat_pvenda' => new MobilePedidoVendaController(),
            'vx_fat_tabprc' => new MobilePrecoProdutoController(),
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
}