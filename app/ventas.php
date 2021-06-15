<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ventas extends Model
{
    protected $table = "ventas";
    public $incrementing = false;
    protected $fillable = ["id","nrof","cod_sucursal","tipo_v","estado","estado_pago","fecha","total_v","ruc_dni_v","nombre_cliente","sucursal","usuario","anulado","fecha_t","created_at","updated_at"];
    protected $primarykey= "id"; 
}
