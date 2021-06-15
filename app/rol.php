<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class rol extends Model
{
    protected $table = "rol";
    public $incrementing = false;
    protected $fillable = ["id","nomrol","created_at","updated_at"];
    protected $primarykey= "id"; 
}
