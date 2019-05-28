<?php

namespace App\Files\PedidoVenda;

/*
 * Make font utilizado para gerar fonts a partir de arquivo ttf;
 *
 */
require(base_path('vendor/anouar/fpdf/src/Anouar/Fpdf/makefont/makefont.php'));

use App\PedidoVenda;
use App\Configuracao;
use Illuminate\Support\Facades\File;

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
        $pdf->SetTitle("Pedido de venda", true);
        $pdf->SetMargins(0, 0, 10);
        $pdf->SetAutoPageBreak(true);
        $pdf->AddPage();


        /*
         * Adiciona Fonte Monteserrat Light - mesma fonte utilizado no PNG feito no canva
         *
         */
        if(!File::exists(base_path('vendor/anouar/fpdf/src/Anouar/Fpdf/font/montserrat-regular.php')) or !File::exists(base_path('vendor/anouar/fpdf/src/Anouar/Fpdf/font/montserrat-regular.z')))
        {
            File::copy(base_path('public/assets/fonts/montserrat-regular.php'),base_path('vendor/anouar/fpdf/src/Anouar/Fpdf/font/montserrat-regular.php'));
            File::copy(base_path('public/assets/fonts/montserrat-regular.z'),base_path('vendor/anouar/fpdf/src/Anouar/Fpdf/font/montserrat-regular.z'));
        }

        if(!File::exists(base_path('vendor/anouar/fpdf/src/Anouar/Fpdf/font/montserrat-semibold.php')) or !File::exists(base_path('vendor/anouar/fpdf/src/Anouar/Fpdf/font/montserrat-semibold.z')))
        {
            File::copy(base_path('public/assets/fonts/montserrat-semibold.php'),base_path('vendor/anouar/fpdf/src/Anouar/Fpdf/font/montserrat-semibold.php'));
            File::copy(base_path('public/assets/fonts/montserrat-semibold.z'),base_path('vendor/anouar/fpdf/src/Anouar/Fpdf/font/montserrat-semibold.z'));
        }
        $pdf->AddFont('MontserratRegular','','montserrat-regular.php');
        $pdf->AddFont('MontserratSemibold','','montserrat-semibold.php');


        $pdf->principal();
        $pdf->cliente();
        $pdf->itens();
        $pdf->impressoPor();


        /*
         * Imprime garantia
         *
         */
        $file = 'pedido-venda'. (isset($pedido->erp_id) ? '-'.$pedido->erp_id : '');

        $pdf->Output($file.'.pdf','I');

        exit;


    }
}


