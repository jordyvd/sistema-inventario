<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class credito_payments extends Model
{
    protected $table = "detalle_notas";
    public $incrementing = false;
    protected $fillable = ["id","monto","created_by","updated_at"];
    protected $primarykey = "id";
}
