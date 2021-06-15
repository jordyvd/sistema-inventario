<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class detalle_traslado extends Model
{
    protected $table = "detalle_traslado";
    public $incrementing = false;
    protected $fillable = ["id","nro_tras","barra_tras","cantidad","de","para","created_at","updated_at"];
    protected $primarykey= "id"; 
}
