<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class almacen extends Model
{
    protected $table = "almacen";
    public $incrementing = false;
    protected $fillable = ["id","barra_almacen","stock_almacen","precio_venta","sucursal","created_at","updated_at"];
    protected $primarykey= "id"; 
}
