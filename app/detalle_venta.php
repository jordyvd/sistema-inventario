<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class detalle_venta extends Model
{
    protected $table = "detalle_venta";
    public $incrementing = false;
    protected $fillable = ["id","barra_detalles","cantidad","precio","descuento","nrof","sucursal","created_at","updated_at"];
    protected $primarykey= "id"; 
}
