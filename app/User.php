<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use Notifiable;

    //The attributes that should be not changed 
    protected $primaryKey = "id";

    //Table's name of the model 
    protected $table = "vx_web_user";

    //The attributes that are table's timestamps 
    public $timestamps = ["created_at", "updated_at"];

    //The attributes that are mass assignable 
    protected $fillable = [];

    //The attributes that should be hidden for arrays.
    protected $hidden = ['password', 'remember_token'];

    //Messages to show when validation fails 
    public static $messages = [];

    //belongsTo empresaFilial
    public function userEmpresaFilial()
    {
        return $this->belongsTo('App\UserEmpresaFilial','vxwebuseref_id','id');
    }

    //belongsTo vendedor
    public function vendedor()
    {
        return $this->belongsTo('App\Vendedor','vxfatvend_id','id');
    }

}
