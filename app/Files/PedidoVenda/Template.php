<?php

namespace App\Files\PedidoVenda;

use Anouar\Fpdf\Fpdf;
use App\Models\Site\Garantia;
use App\PedidoVenda;
use App\Configuracao;
use Illuminate\Support\Facades\Auth;
use App\Utils\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class Template extends FPDF
{
    public $pedido;
    public $clienteInfo;
    public $configuracao;

    function __construct($orientation='P', $unit='mm', $size='A4', PedidoVenda $pedido, Configuracao $configuracao)
    {
        parent::__construct($orientation, $unit, $size);

        $this->pedido       = $pedido;
        $this->clienteInfo  = json_decode($pedido->cliente_data);
        $this->configuracao = $configuracao;


        //Adiciona Fonte Monteserrat Light
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

        $this->AddFont('MontserratRegular','','montserrat-regular.php');
        $this->AddFont('MontserratSemibold','','montserrat-semibold.php');

    }


    function Header()
    {

        if(isset($this->configuracao->pdf_template))
        {
            $this->Image(base_path("public".$this->configuracao->pdf_template), 0, 0);
        }


        //código do pedido
        $this->SetXY( 22, $this->y);
        $this->SetFont( "MontserratSemibold", "", 24);
        $this->SetTextColor(72,72,70);
        if($this->pedido->erp_id !== null)
        {
            $this->MultiCell(0,10,utf8_decode("Pedido de venda #".str_pad($this->pedido->erp_id,'6','0',STR_PAD_LEFT)),0,'L', FALSE);
        }
        else
        {
            $this->MultiCell(0,10,utf8_decode("Pedido de venda"),0,'L', FALSE);
        }

        //logo da empresa
        if(! isset($this->configuracao->logo_empresa))
        {
            $logo = base_path("public/assets/img/logo/vex_splash.png");

            $this->Image($logo, 140, 0, 80, 80);
        }
        else
        {
            $logo = base_path("public".$this->configuracao->logo_empresa);

            list($width, $height) = getimagesize(base_path("public".$this->configuracao->logo_empresa));

            if($width <= 80)
            {
                $this->Image($logo, 140, 0, $width, $height);
            }
            else
            {
                $h = ($height * 40) / $width;

                $this->Image($logo, 155, 20, 40, $h);
            }
        }


        /**
         *  MultiCell utilizado para quebrar linha com próximo elemento
         *
         */
        $this->MultiCell(0,10,utf8_decode(""),0,'L', FALSE);

    }



    function Footer()
    {
        //impresso por
        $this->SetFont( "MontserratRegular", "", 8);
        $this->SetTextColor(72,72,70);

        $this->SetXY(22, 270);
        $this->MultiCell(0,1.5,utf8_decode("Impressor por: \n\n\n".
Auth::user()->name."\n\n\n".
Auth::user()->email."\n\n\n".
Carbon::now()->format('d/m/Y - H:i:s')."\n"),0,'L', FALSE);

        //paginação
        $this->SetFont( "MontserratSemibold", "", 8);
        $this->SetXY( 22, -12);
        $pagina = $this->PageNo().'/'.count($this->pages);
        $this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'C');
    }


    function principal()
    {
        $y = $this->y;

        //empresa
        $this->SetTextColor(72,72,70);
        $this->SetXY( 22, $y);
        $this->SetFont( "MontserratSemibold", "", 10);
        $this->MultiCell(40,10,utf8_decode("Empresa:"),0,'L', FALSE);
        $this->SetXY( 62, $y);
        $this->MultiCell(0,10,utf8_decode($this->pedido->empfil->nome),0,'L', FALSE);

        //empresa
        $this->SetTextColor(72,72,70);
        $this->SetXY( 22, $y + 6);
        $this->SetFont( "MontserratSemibold", "", 10);
        $this->MultiCell(40,10,utf8_decode("CNPJ:"),0,'L', FALSE);
        $this->SetXY( 62, $y + 6);
        $this->MultiCell(0,10,utf8_decode(Helper::insereMascara($this->pedido->empfil->cnpj,'##.###.###/####-##')),0,'L', FALSE);

        //vendedor
        $this->SetTextColor(72,72,70);
        $this->SetXY( 22, $y + 12);
        $this->SetFont( "MontserratSemibold", "", 10);
        $this->MultiCell(40,10,utf8_decode("Vendedor:"),0,'L', FALSE);
        $this->SetXY( 62, $y + 12);
        $this->MultiCell(0,10,utf8_decode($this->pedido->vendedor->nome),0,'L', FALSE);

        //data e hora do pedido
        $this->SetTextColor(72,72,70);
        $this->SetXY( 22, $y + 18);
        $this->SetFont( "MontserratSemibold", "", 10);
        $this->MultiCell(40,10,utf8_decode("Data do pedido:"),0,'L', FALSE);
        $this->SetXY( 62, $y + 18);
        $this->MultiCell(0,10,utf8_decode(Carbon::createFromFormat('Y-m-d H:i:s',$this->pedido->created_at)->format('d/m/Y à\s H:i')),0,'L', FALSE);
    }



    function cliente()
    {
        $y = $this->y;

        $this->SetFont( "MontserratSemibold", "", 8);
        $this->SetDrawColor(230,230,230);
        $this->SetFillColor(230,230,230);

        //linha
        $this->RoundedRect(22, $this->y + 6, 170, 0.03, 0, 'DF');

        //cliente
        $this->SetTextColor(72,72,70);
        $this->SetXY( 22, $y + 12);
        $this->SetFont( "MontserratSemibold", "", 8);
        $this->MultiCell(40,8,utf8_decode("Cliente:"),0,'L', FALSE);
        $this->SetXY( 62, $y + 12);
        $this->MultiCell(0,8,utf8_decode($this->clienteInfo->razao_social),0,'L', FALSE);

        //cnpj
        $this->SetTextColor(72,72,70);
        $this->SetXY( 22, $y + 18);
        $this->MultiCell(40,8,utf8_decode("CPF/CNPJ:"),0,'L', FALSE);
        $this->SetXY( 62, $y + 18);
        $this->MultiCell(0,8,utf8_decode(Helper::insereMascara($this->clienteInfo->cnpj_cpf, $this->clienteInfo->tipo_pessoa == 'J' ? '##.###.###/####-##': '###.###.###-##')),0,'L', FALSE);

        //endereço formatado
        $endereco = $this->clienteInfo->endereco;

        $bairro = '';

        if($this->clienteInfo->bairro !== null and $this->clienteInfo->bairro !== '')
        {
            $bairro .= 'Bairro '.$this->clienteInfo->bairro;
        }

        if($this->clienteInfo->cep !== null and $this->clienteInfo->cep !== '')
        {
            $bairro .= ', CEP: '.Helper::insereMascara($this->clienteInfo->cep,'#####-###');
        }

        $cidade = '';

        if($this->clienteInfo->cidade !== null and $this->clienteInfo->cidade !== '')
        {
            if($this->clienteInfo->cep !== null and $this->clienteInfo->cep !== '')
            {
                $cidade .= ' - ';
            }

            $cidade .= $this->clienteInfo->cidade.'/'.$this->clienteInfo->uf;
        }


        $this->SetTextColor(72,72,70);
        $this->SetXY( 22, $y + 24);
        $this->MultiCell(40,8,utf8_decode("Endereço:"),0,'L', FALSE);
        $this->SetXY( 62, $y + 24);
        $this->MultiCell(0,8,utf8_decode($endereco),0,'L', FALSE);

        $this->SetXY( 62, $y + 30);
        $this->MultiCell(0,8,utf8_decode($bairro . $cidade),0,'L', FALSE);



        //telefone formatado
        $fone = '';

        if($this->clienteInfo->ddd !== null and $this->clienteInfo->ddd !== '')
        {
            $fone .= '('.$this->clienteInfo->ddd.') ';
        }

        if($this->clienteInfo->fone !== null and $this->clienteInfo->fone !== '')
        {
            if(strlen($this->clienteInfo->fone) == 9)
            {
                $fone .= Helper::insereMascara($this->clienteInfo->fone,'# ####-####');
            }
            elseif(strlen($this->clienteInfo->fone) == 8)
            {
                $fone .= Helper::insereMascara($this->clienteInfo->fone,'####-####');
            }
            else
            {
                $fone .= $this->clienteInfo->fone;
            }
        }

        $this->SetTextColor(72,72,70);
        $this->SetXY( 22, $y + 36);
        $this->MultiCell(40,8,utf8_decode("Fone:"),0,'L', FALSE);
        $this->SetXY( 62, $y + 36);
        $this->MultiCell(0,8,utf8_decode($fone),0,'L', FALSE);

        //email
        $this->SetTextColor(72,72,70);
        $this->SetXY( 22, $y + 42);
        $this->MultiCell(40,8,utf8_decode("E-mail:"),0,'L', FALSE);
        $this->SetXY( 62, $y + 42);
        $this->MultiCell(0,8,utf8_decode(isset($this->clienteInfo->email) ? $this->clienteInfo->email : 'Não informado'),0,'L', FALSE);
    }


    function condicoes()
    {
        $fillColor = $this->color('fill');
        $textColor = $this->color('text');


        $this->y = $this->y + 10;
        $this->SetDrawColor($fillColor['r'], $fillColor['g'], $fillColor['b']);
        $this->SetFillColor($fillColor['r'], $fillColor['g'], $fillColor['b']);
        $this->SetTextColor($textColor['r'], $textColor['g'], $textColor['b']);
        $this->SetXY( 22, $this->y);
        $this->Cell(55,8,'Data de entrega',1,0,'C',1);
        $this->SetXY( 77, $this->y);
        $this->Cell(60,8,'Cond. Pagamento',1,0,'C',1);
        $this->SetXY( 137, $this->y);
        $this->Cell(55,8,'Valor total (R$)',1,1,'C',1);

        $this->SetTextColor(72,72,70);
        $this->SetFont( "MontserratSemibold", "", 8);
        $this->SetXY( 22, $this->y);
        $this->Cell(55,8,Carbon::createFromFormat('Y-m-d',$this->pedido->data_entrega)->format('d/m/Y'),1,0,'C',0);
        $this->SetXY( 77, $this->y);
        $this->Cell(60,8,utf8_decode($this->pedido->condicao->descricao),1,0,'C',0);
        $this->SetXY( 137, $this->y);
        $this->Cell(55,8,number_format($this->pedido->valorTotal(),2,',','.'),1,1,'C',0);
    }


    function itens()
    {
        $fillColor = $this->color('fill');

        $this->cabecalhoItens($this->y + 10);

        $this->SetFont( "MontserratSemibold", "", 8);
        $this->SetDrawColor($fillColor['r'], $fillColor['g'], $fillColor['b']);
        $this->SetFillColor($fillColor['r'], $fillColor['g'], $fillColor['b']);
        $this->SetDrawColor(210,210,210);
        $this->SetFillColor(210,210,210);


        $total = 0.00;

        $index = 0;

        foreach($this->pedido->itens as $item)
        {
            $index++;

            $y = $this->y;

            if($index == 1)
            {
                $y++;
            }

            $total = $total + $item->valor_total;


            //descrição do produto
            $this->SetFont( "MontserratSemibold", "", 7);
            $this->SetTextColor(72,72,70);
            $this->SetXY( 22, $y + 1);
            $this->MultiCell(124,3,utf8_decode(json_decode($item->produto_data)->descricao),0,'L',false);

            //código erp e unidade de medida do produto
            $this->SetXY( 22, $y + 8);
            $this->SetFont( "MontserratRegular", "", 7);
            $this->MultiCell(124,3,utf8_decode('Cód: '. json_decode($item->produto_data)->erp_id.'  - Unid: '.json_decode($item->produto_data)->unidade_principal),0,'L', false);

            //lote do produto
            $this->SetXY( 22, $y + 12);
            $this->SetFont( "MontserratRegular", "", 7);
            try
            {
                $lote   = $item->lote->erp_id;
                $fabric = Carbon::createFromFormat('Y-m-d',$item->lote->dt_fabric)->format('d/m/Y');
                $valid  = Carbon::createFromFormat('Y-m-d',$item->lote->dt_valid)->format('d/m/Y');
                $this->MultiCell(124,3,utf8_decode("Lote: $lote - Dt Fab: $fabric - Dt Valid: $valid"),0, 'L', false);
            }
            catch(\Exception $e)
            {
                $this->MultiCell(124,3,utf8_decode("Lote não identificado"),0, 'L', false);
            }


            //quantidade
            $this->SetXY( 146, $y + 2);
            $this->MultiCell(10,9,utf8_decode(number_format($item->quantidade,0,',','')),0,'R', false);

            //preço unitário
            $this->SetXY( 156, $y + 2);
            $this->MultiCell(18,9,utf8_decode(number_format($item->preco_unitario,2,',','.')),0,'R', false);

            //preço total do item
            $this->SetXY( 174, $y + 2);
            $this->MultiCell(18,9,utf8_decode(number_format($item->valor_total,2,',','.')),0,'R', false);

            //linha
            $this->RoundedRect(22, $y + 18, 171, 0.01, 0, 'DF');


            if($this->y > 238 and $index < count($this->pedido->itens))
            {
                $this->AddPage();
                $this->cabecalhoItens($this->y + 10);

                $this->SetFont( "MontserratSemibold", "", 8);
                $this->SetDrawColor($fillColor['r'], $fillColor['g'], $fillColor['b']);
                $this->SetFillColor($fillColor['r'], $fillColor['g'], $fillColor['b']);
                $this->SetDrawColor(210,210,210);
                $this->SetFillColor(210,210,210);

            }
            else
            {
                $this->y = $this->y + 8;
            }

        }



        $this->SetFont( "MontserratSemibold", "", 8);
        $y = $y + 18;


        //valor
        $this->SetTextColor(72,72,70);
        $this->SetFont( "MontserratSemibold", "", 10);
        $this->SetXY( 132, $y + 2);
        $this->Cell(30,8,utf8_decode('Valor total:'),0,0,'R');

        //valor
        $this->SetTextColor(72,72,70);
        $this->SetXY( 162, $y + 2);
        $this->Cell(30,8,utf8_decode('R$  '.number_format($total,2,',','.')),0,0,'R');

    }


    function cabecalhoItens($y)
    {
        $fillColor = $this->color('fill');
        $textColor = $this->color('text');

        $this->SetFont( "MontserratSemibold", "", 7);
        $this->SetDrawColor($fillColor['r'], $fillColor['g'], $fillColor['b']);
        $this->SetFillColor($fillColor['r'], $fillColor['g'], $fillColor['b']);
        $this->SetTextColor($textColor['r'], $textColor['g'], $textColor['b']);
        $this->SetXY( 22, $y);
        $this->Cell(124,8,utf8_decode('  Produto'),0,0,'L',1);
        $this->SetXY( 146, $y);
        $this->Cell(10,8,utf8_decode('Qtd'),0,0,'R',1);
        $this->SetXY( 156, $y);
        $this->Cell(18,8,utf8_decode('Pç unit (R$)'),0,0,'R',1);
        $this->SetXY( 174, $y);
        $this->Cell(18,8,utf8_decode('Pç final (R$)'),0,1,'R',1);
    }


    function color($type = 'fill')
    {
        $fillColor = ['r'=>240,'g'=>240,'b'=>240];
        $textColor = ['r'=>72,'g'=>72,'b'=>70];

        if(isset($this->configuracao->pdf_template))
        {
            if(strpos($this->configuracao->pdf_template, 'aqua'))
            {
                $fillColor = ['r'=>53,'g'=>170,'b'=>178];
                $textColor = ['r'=>255,'g'=>255,'b'=>255];
            }
            elseif(strpos($this->configuracao->pdf_template, 'grey'))
            {
                $fillColor = ['r'=>180,'g'=>180,'b'=>180];
                $textColor = ['r'=>255,'g'=>255,'b'=>255];
            }
            elseif(strpos($this->configuracao->pdf_template, 'green'))
            {
                $fillColor = ['r'=>145,'g'=>176,'b'=>124];
                $textColor = ['r'=>255,'g'=>255,'b'=>255];
            }
            elseif(strpos($this->configuracao->pdf_template, 'red'))
            {
                $fillColor = ['r'=>252,'g'=>84,'b'=>85];
                $textColor = ['r'=>255,'g'=>255,'b'=>255];
            }
            elseif(strpos($this->configuracao->pdf_template, 'yellow'))
            {
                $fillColor = ['r'=>252,'g'=>226,'b'=>163];
                $textColor = ['r'=>72,'g'=>72,'b'=>70];
            }
        }

        if($type == 'fill')
        {
            return $fillColor;
        }
        else
        {
            return $textColor;
        }
    }


    /**
     * Private functions
     *
     */

    // private functions
    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
        $xc = $x+$w-$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
        $xc = $x+$w-$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x+$r ;
        $yc = $y+$h-$r;$this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }




}


