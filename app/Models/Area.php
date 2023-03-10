<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $fillable = ['name','city_id'];

    public function city()
    {
        return $this->belongsTo('App\Models\City','city_id');
    }

    public function company()
    {
        return $this->hasMany('App\Models\Company','area_id');
    }

    public function receivers()
    {
        return $this->hasMany('App\Models\Receiver','area_id');
    }

    public function awb()
    {
        return $this->hasMany('App\Models\Awb','area_id');
    }
}





