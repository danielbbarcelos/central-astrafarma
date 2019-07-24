<?php

namespace App\Files\PedidoVenda;

/*
 * Make font utilizado para gerar fonts a partir de arquivo ttf;
 *
 */
require(base_path('vendor/anouar/fpdf/src/Anouar/Fpdf/makefont/makefont.php'));

use App\PedidoVenda;
use App\Configuracao;

class Main
{
    public function generate(PedidoVenda $pedido, Configuracao $configuracao)
    {

        /*
         *      PAISAGEM = TOTAL HORIZONTAL É 276
         *
         *      RETRATO = TOTAL HORIZONTAL É 210
         */
        $pdf = new Template( 'P', 'mm', 'A4', $pedido, $configuracao);
        $pdf->AliasNbPages(); //gera alias de contador de páginas
        $pdf->SetTitle("Pedido de venda", true);
        $pdf->SetMargins(10, 20, 10);
        $pdf->SetAutoPageBreak(true);
        $pdf->AddPage();



        $pdf->principal();
        $pdf->cliente();
        $pdf->condicoes();
        $pdf->itens();


        /*
         * Imprime garantia
         *
         */
        $file = 'pedido-venda'. (isset($pedido->erp_id) ? '-'.$pedido->erp_id : '');

        $pdf->Output($file.'.pdf','I');

        exit;


    }
}


