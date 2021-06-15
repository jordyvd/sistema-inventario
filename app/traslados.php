<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class traslados extends Model
{
    protected $table = "traslados";
    public $incrementing = false;
    protected $fillable = ["id","nro","cod_sucursal","sucursal","para","estado","recibido","created_at","updated_at"];
    protected $primarykey= "id"; 
}
