<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class detalle_notas extends Model
{
    protected $table = "detalle_notas";
    public $incrementing = false;
    protected $fillable = ["id","barra","precio_com","descuento","cantidad","cod_nota","created_at","updated_at"];
    protected $primarykey = "id";
}
