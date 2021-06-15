<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notas extends Model
{
    protected $table = "notas";
    public $incrementing = false;
    protected $fillable = ["id","codigo_nota","sucursal","total","ruc_provee","empresa","fecha","estado","anulado","created_at","updated_at"];
    protected $primarykey= "id"; 
}
