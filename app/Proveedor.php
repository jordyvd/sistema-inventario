<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = "proveedor";
    public $incrementing = false;
    protected $fillable = ["id","ruc","nombre","created_at","updated_at"];
    protected $primarykey= "id"; 
}
