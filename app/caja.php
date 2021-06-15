<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class caja extends Model
{
     protected $table = "caja";
     public $incrementing = false;
     protected $fillable = ["id","cantidad","fecha","sucursal","created_at","updated_at"];
     protected $primarykey = "id";

}
