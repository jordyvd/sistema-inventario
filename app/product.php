<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    protected $table = "products";
    public $incrementing = false;
    protected $fillable = ["id","codigo","barra","nompro","precio","stock","marca","url_imagen","baja","created_at","updated_at","day"];
    protected $primarykey= "id"; 
}
