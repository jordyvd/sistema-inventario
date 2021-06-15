<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ingresos_salidas extends Model
{
    protected $table = "ingresos_salidas";
    public $incrementing = false;
    protected $fillable = ["id","descripcion","monto","condicion","fecha","sucursal","created_at","updated_at"];
    protected $primarykey= "id"; 
}

