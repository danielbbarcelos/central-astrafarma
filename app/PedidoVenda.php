<?php 

namespace App; 

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 
use Carbon\Carbon; 
use App\Assinatura;
use App\PedidoItem;
use App\Utils\Helper;

class PedidoVenda extends Model
{

    //The webservice's prefix of this model
    protected $webservice = "/rest/vxfatpvenda";

    //The attributes that should be not changed 
    protected $primaryKey = "id";

    //Table's name of the model 
    protected $table = "vx_fat_pvenda";

    //The attributes that are table's timestamps 
    public $timestamps = ["created_at", "updated_at"];

    //The attributes that should be hidden for arrays 
    protected $hidden = [];

    //Messages to show when validation fails 
    public static $messages = [
        'cliente_id.required'             => 'Cliente não informado',
        'condicao_pagamento_id.required'  => 'Condição de pagamento não informado',
        'tabela_preco_id.required'        => 'Preço não informado',
        'vendedorid.required'             => 'Vendedor não informado',
    ];

    //return table's name of this model
    public function getTable()
    {
        return $this->table;
    }

    //return webservice of this model
    public function getWebservice($action = '')
    {
        return $this->webservice.'/'.$action;
    }

    //belongsTo empfil
    public function empfil()
    {
        return $this->belongsTo('App\EmpresaFilial','vxgloempfil_id','id');
    }

    //belongsTo vendedor
    public function vendedor()
    {
        return $this->belongsTo('App\Vendedor','vxfatvend_erp_id','erp_id');
    }

    //belongsTo cliente
    public function cliente()
    {
        return $this->belongsTo('App\Cliente','vxglocli_erp_id','erp_id');
    }

    //belongsTo condicao
    public function condicao()
    {
        return $this->belongsTo('App\CondicaoPagamento','vxglocpgto_erp_id','erp_id');
    }

    //hasMany itens
    public function itens()
    {
        return $this->hasMany('App\PedidoItem','vxfatpvenda_id','id');
    }

    //retorna o valor total do pedido
    public function valorTotal()
    {
        $total = 0.00;

        $itens = PedidoItem::where('vxfatpvenda_id',$this['id'])->get();

        foreach($itens as $item)
        {
            $total = $total + $item->valor_total;
        }

        return $total;
    }

}
