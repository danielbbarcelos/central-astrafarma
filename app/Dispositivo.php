<?php 

namespace App; 

use Illuminate\Database\Eloquent\Model; 

class Dispositivo extends Model
{

    //The attributes that should be not changed 
    protected $primaryKey = "id";

    //Table's name of the model 
    protected $table = "vx_web_disp";

    //The attributes that are table's timestamps 
    public $timestamps = ["created_at", "updated_at"];

    //The attributes that are mass assignable 
    protected $fillable = [];

    //The attributes that should be hidden for arrays 
    protected $hidden = [];

    //Messages to show when validation fails 
    public static $messages = [
        'descricao.required'    => 'Descrição não informada',
        'descricao.unique'      => 'A descrição já está sendo utilizada',
        'device_id.required'    => 'Device ID não informado',
        'device_id.unique'      => 'O Device ID já está sendo utilizado',
        'mac.required'          => 'Endereço MAC não informado',
        'mac.unique'            => 'O Endereço MAC já está sendo utilizado',
    ];

}