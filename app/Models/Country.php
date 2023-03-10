<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $fillable = ['name','icon'];


    public function city(){
        return $this->hasMany('App\Models\City','country_id');
    }
}
