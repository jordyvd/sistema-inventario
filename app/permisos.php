<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class permisos extends Model
{
    protected $table = "permisos";
    public $incrementing = false;
    protected $fillable = ["id","traslados","ventas","sucursal_ventas","reportes","reporte_ingres","reporte_egreso","ganancias","almacen","clientes","mantenimiento","agregar_modif_mante","permisos","rol","compras","created_at","updated_at"];
    protected $primarykey= "id"; 
}
