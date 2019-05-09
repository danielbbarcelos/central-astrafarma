<?php 

namespace App; 

use Illuminate\Database\Eloquent\Model; 

class Chamado extends Model
{
    //original connection to table
    protected $connection = 'admin';

    //The attributes that should be not changed 
    protected $primaryKey = "id";

    //Table's name of the model 
    protected $table = "chamados";

    //The attributes that are table's timestamps 
    public $timestamps = ["created_at", "updated_at"];

    //The attributes that are mass assignable 
    protected $fillable = [];

    //The attributes that should be hidden for arrays 
    protected $hidden = [];

    //Messages to show when validation fails 
    public static $messages = [];

}