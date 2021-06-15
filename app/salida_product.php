<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class salida_product extends Model
{
    protected $table = "salida_product";
    public $incrementing = false;
    protected $fillable = ["id","cod_sucursal","nrof","condicion","total","descripcion","fecha","sucursal","created_at","updated_at"];
    protected $primarykey= "id"; 
}
