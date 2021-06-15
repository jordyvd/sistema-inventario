<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class sucursal extends Model
{
    protected $table = "sucursal";
    public $incrementing = false;
    protected $fillable = ["id","nombre","created_at","updated_at"];
    protected $primarykey= "id"; 
}
