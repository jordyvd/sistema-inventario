<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class detalle_salidap extends Model
{
    protected $table = "detalle_salidap";
    public $incrementing = false;
    protected $fillable = ["id","barra_salida","precio","cantidad","descuento","nrof","nrof","created_at","updated_at"];
    protected $primarykey= "id"; 
}
