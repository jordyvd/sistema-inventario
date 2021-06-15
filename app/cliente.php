<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cliente extends Model
{
    protected $table = "cliente";
    public $incrementing = false;
    protected $fillable = ["id","nombre","ruc_dni","telefono","direccion","created_at","updated_at"];
    protected $primarykey= "id"; 
}
