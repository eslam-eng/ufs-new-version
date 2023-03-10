<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name','country_id'];

    public function country()
    {
        return $this->belongsTo('App\Models\Country','country_id');
    }
    public function area()
    {
        return $this->hasMany('App\Models\Area','city_id');
    }

    public function company()
    {
        return $this->hasMany('App\Models\Company','city_id');
    }

    public function receiver()
    {
        return $this->hasMany('App\Models\Receiver','city_id');
    }

    public function awb()
    {
        return $this->hasMany('App\Models\Awb','city_id');
    }

}
