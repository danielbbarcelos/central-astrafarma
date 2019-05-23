<?php 

namespace App; 

use Illuminate\Database\Eloquent\Model; 
use App\Assinatura;
use App\Utils\Helper;
use Illuminate\Database\Eloquent\SoftDeletes;

class PedidoItem extends Model
{
    use SoftDeletes;

    //The attributes that should be not changed 
    protected $primaryKey = "id";

    //Table's name of the model 
    protected $table = "vx_fat_ipvend";

    //The attributes that are table's timestamps 
    public $timestamps = ["created_at", "updated_at"];

    //The attributes that should be hidden for arrays 
    protected $hidden = [];

    //Messages to show when validation fails 
    public static $messages = [];

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

    //belongsTo produto
    public function produto()
    {
        return $this->belongsTo('App\Produto', 'vxgloprod_erp_id','erp_id');
    }

}