<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class marca extends Model
{
    protected $table = "marcas";
    public $incrementing = false;
    protected $fillable = ["id","nommar","created_at","updated_at"];
    protected $primarykey= "id"; 
}
