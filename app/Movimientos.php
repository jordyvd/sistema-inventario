<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movimientos extends Model
{
    protected $table = "movimientos";
    public $incrementing = false;
    protected $fillable = ["id","nro_documento","barra_mov","precio","condicion","fecha","detalle","tipo","sucursal","created_at","updated_at"];
    protected $primarykey= "id"; 
}
