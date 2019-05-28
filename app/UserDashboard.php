<?php 

namespace App; 

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon; 
use App\Assinatura;
use App\Utils\Helper;

class UserDashboard extends Model
{
    use SoftDeletes;

    //The webservice's prefix of this model
    protected $webservice = null;

    //The attributes that should be not changed 
    protected $primaryKey = "id";

    //Table's name of the model 
    protected $table = "vx_web_userds";

    //The attributes that are table's timestamps 
    public $timestamps = ["created_at", "updated_at"];

    //The attributes that are mass assignable 
    protected $fillable = [];

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

    //belongsTo user
    public function user()
    {
        return $this->belongsTo('App\User','vxwebuser_id');
    }

}