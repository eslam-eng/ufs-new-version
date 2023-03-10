<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = ['name','company_id','phone','address','city_id','area_id'];

    public function Company()
    {
        return $this->belongsTo('App\Models\Company','company_id');
    }
    public function city()
    {
        return $this->belongsTo('App\Models\City','city_id');
    }
    public function area()
    {
        return $this->belongsTo('App\Models\Area','area_id');
    }
    public function couriers()
    {
        return $this->hasMany('App\Models\Courier','branch_id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User','branch_id');
    }

    public function awb()
    {
        return $this->hasMany('App\Models\Awb','branch_id');
    }
}
