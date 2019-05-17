<?php

namespace App\Files\PedidoVenda;

use Anouar\Fpdf\Fpdf;
use App\Models\Site\Garantia;
use App\PedidoVenda;
use App\PedidoVendaConfiguracao;
use Illuminate\Support\Facades\Auth;
use App\Utils\Helper;
use Carbon\Carbon;

class Template extends FPDF
{
    public $pedido;
    public $clienteInfo;
    public $configuracao;

    function __construct($orientation='P', $unit='mm', $size='A4', PedidoVenda $pedido, PedidoVendaConfiguracao $configuracao)
    {
        parent::__construct($orientation, $unit, $size);

        $this->pedido       = $pedido;
        $this->clienteInfo  = json_decode($pedido->cliente_data);
        $this->configuracao = $configuracao;
    }


    function Header()
    {
        if(isset($this->configuracao->pdf_template))
        {
            $this->Image(base_path("public".$this->configuracao->pdf_template));
        }
    }


    function Footer()
    {
        $this->SetFont( "MontserratSemibold", "", 8);
        $this->SetXY( 22, -12);
        $pagina = $this->PageNo().'/'.count($this->pages);
        $this->Cell(0,10,utf8_decode('Página '.$pagina),0,0,'C');
    }


    function principal()
    {
        $y = 20;

        //código do pedido
        $this->SetXY( 22, $y);
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
        $logo = isset($configuracao) ? base_path("public".$this->configuracao->pdf_template) : base_path("public/assets/img/logo/vex_splash.png");
        $this->Image($logo, 140, 0, 80, 80);

        $y = $y + 4;

        //empresa
        $this->SetTextColor(72,72,70);
        $this->SetXY( 22, $y + 12);
        $this->SetFont( "MontserratSemibold", "", 10);
        $this->MultiCell(40,10,utf8_decode("Empresa:"),0,'L', FALSE);
        $this->SetXY( 62, $y + 12);
        $this->MultiCell(0,10,utf8_decode($this->pedido->empfil->nome),0,'L', FALSE);

        //empresa
        $this->SetTextColor(72,72,70);
        $this->SetXY( 22, $y + 18);
        $this->SetFont( "MontserratSemibold", "", 10);
        $this->MultiCell(40,10,utf8_decode("CNPJ:"),0,'L', FALSE);
        $this->SetXY( 62, $y + 18);
        $this->MultiCell(0,10,utf8_decode(Helper::insereMascara($this->pedido->empfil->cnpj,'##.###.###/####-##')),0,'L', FALSE);

        //vendedor
        $this->SetTextColor(72,72,70);
        $this->SetXY( 22, $y + 24);
        $this->SetFont( "MontserratSemibold", "", 10);
        $this->MultiCell(40,10,utf8_decode("Vendedor:"),0,'L', FALSE);
        $this->SetXY( 62, $y + 24);
        $this->MultiCell(0,10,utf8_decode($this->pedido->vendedor->nome),0,'L', FALSE);

        //data e hora do pedido
        $this->SetTextColor(72,72,70);
        $this->SetXY( 22, $y + 30);
        $this->SetFont( "MontserratSemibold", "", 10);
        $this->MultiCell(40,10,utf8_decode("Data do pedido:"),0,'L', FALSE);
        $this->SetXY( 62, $y + 30);
        $this->MultiCell(0,10,utf8_decode(Carbon::createFromFormat('Y-m-d H:i:s',$this->pedido->created_at)->format('d/m/Y à\s H:i')),0,'L', FALSE);
    }


    function cliente()
    {
        $y = 60;

        $fillColor = $this->color('fill');
        $textColor = $this->color('text');

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

        if($this->clienteInfo->endereco_numero !== null and $this->clienteInfo->endereco_numero !== '')
        {
            $endereco .= ', '.$this->clienteInfo->endereco_numero;
        }

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



    function itens()
    {
        $fillColor = $this->color('fill');
        $textColor = $this->color('text');



        $this->y = $this->y + 15;
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
        $this->SetFont( "MontserratSemibold", "", 10);
        $this->SetXY( 22, $this->y);
        $this->Cell(55,8,Carbon::createFromFormat('Y-m-d',$this->pedido->data_entrega)->format('d/m/Y'),1,0,'C',0);
        $this->SetXY( 77, $this->y);
        $this->Cell(60,8,utf8_decode($this->pedido->condicao->descricao),1,0,'C',0);
        $this->SetXY( 137, $this->y);
        $this->Cell(55,8,number_format($this->pedido->valorTotal(),2,',','.'),1,1,'C',0);





        $this->y = $this->y + 15;
        $this->SetFont( "MontserratSemibold", "", 8);
        $this->SetDrawColor($fillColor['r'], $fillColor['g'], $fillColor['b']);
        $this->SetFillColor($fillColor['r'], $fillColor['g'], $fillColor['b']);
        $this->SetTextColor($textColor['r'], $textColor['g'], $textColor['b']);
        $this->SetXY( 22, $this->y);
        $this->Cell(15,8,utf8_decode('ID'),0,0,'C',1);
        $this->SetXY( 37, $this->y);
        $this->Cell(50,8,utf8_decode('Descrição'),0,0,'L',1);
        $this->SetXY( 87, $this->y);
        $this->Cell(15,8,utf8_decode('UM'),0,0,'C',1);
        $this->SetXY( 102, $this->y);
        $this->Cell(30,8,utf8_decode('Preço un. (R$)'),0,0,'C',1);
        $this->SetXY( 132, $this->y);
        $this->Cell(30,8,utf8_decode('Desconto (R$)'),0,0,'C',1);
        $this->SetXY( 162, $this->y);
        $this->Cell(30,8,utf8_decode('Preço final (R$)'),0,1,'C',1);



        $this->SetFont( "MontserratSemibold", "", 8);
        $this->SetDrawColor($fillColor['r'], $fillColor['g'], $fillColor['b']);
        $this->SetFillColor($fillColor['r'], $fillColor['g'], $fillColor['b']);

        //linha
        $this->RoundedRect(22, $this->y, 170, 0.03, 0, 'DF');

        $subtotal = 0.00;
        $desconto = 0.00;
        $total    = 0.00;

        foreach($this->pedido->itens as $item)
        {
            $subtotal = $subtotal + ($item->preco_unitario * $item->quantidade);
            $desconto = $desconto + $item->valor_desconto;
            $total    = $total    + $item->valor_total;

            $this->SetTextColor(72,72,70);
            $this->SetXY( 22, $this->y);
            $this->Cell(15,8,utf8_decode($item->id),0,0,'C');
            $this->SetXY( 37, $this->y);
            $this->Cell(50,8,utf8_decode(json_decode($item->produto_data)->descricao),0,0,'L');
            $this->SetXY( 87, $this->y );
            $this->Cell(15,8,utf8_decode(json_decode($item->produto_data)->unidade_principal),0,0,'C');
            $this->SetXY( 102, $this->y);
            $this->Cell(30,8,utf8_decode(number_format($item->preco_unitario,2,',','.')),0,0,'C');
            $this->SetXY( 132, $this->y);
            $this->Cell(30,8,utf8_decode(number_format($item->valor_desconto,2,',','.')),0,0,'C');
            $this->SetXY( 162, $this->y);
            $this->Cell(30,8,utf8_decode(number_format($item->valor_total,2,',','.')),0,1,'C');

            //linha
            $this->RoundedRect(22, $this->y, 170, 0.03, 0, 'DF');
        }


        $this->SetFont( "MontserratSemibold", "", 8);
        $y = $this->y + 6;


        //subtotal
        $this->SetTextColor(72,72,70);
        $this->SetXY( 132, $y + 2);
        $this->Cell(30,8,utf8_decode('Subtotal:'),0,0,'R');
        //desconto
        $this->SetTextColor(252,84,85);
        $this->SetXY( 132, $y + 10);
        $this->Cell(30,8,utf8_decode('Desconto:'),0,0,'R');
        //valor
        $this->SetTextColor(72,72,70);
        $this->SetFont( "MontserratSemibold", "", 10);
        $this->SetXY( 132, $y + 18);
        $this->Cell(30,8,utf8_decode('Valor total:'),0,0,'R');


        $this->SetFont( "MontserratSemibold", "", 8);

        //subtotal
        $this->SetTextColor(72,72,70);
        $this->SetXY( 162, $y + 2);
        $this->Cell(30,8,utf8_decode('R$  '.number_format($subtotal,2,',','.')),0,0,'R');
        //desconto
        $this->SetTextColor(252,84,85);
        $this->SetXY( 162, $y + 10);
        $this->Cell(30,8,utf8_decode('R$  '.number_format($desconto,2,',','.')),0,0,'R');
        //valor
        $this->SetTextColor(72,72,70);
        $this->SetFont( "MontserratSemibold", "", 10);
        $this->SetXY( 162, $y + 18);
        $this->Cell(30,8,utf8_decode('R$  '.number_format($total,2,',','.')),0,0,'R');

    }



    function impressoPor()
    {
        $this->SetFont( "MontserratRegular", "", 8);
        $this->SetTextColor(72,72,70);

        $this->SetXY(22, 270);
        $this->MultiCell(0,1.5,utf8_decode("Impressor por: \n\n\n".
Auth::user()->name."\n\n\n".
Auth::user()->email."\n\n\n".
Carbon::now()->format('d/m/Y - H:i:s')."\n"),0,'L', FALSE);

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


