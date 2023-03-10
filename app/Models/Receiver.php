<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receiver extends Model
{
    use HasFactory;
    protected $fillable = [
        'name','address','phone','company_id','city_id','area_id',
        'branch_id', 'company_name', 'branch_name', 'address2','referance', 'title'
    ];

    protected $appends = [
        'search'
    ];

    public function getSearchAttribute() {
        return $this->name . "-" . $this->company_name . "-" . $this->referance;
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company','company_id')->select('id', 'name', 'logo');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City','city_id')->with('country');
    }

    public function area()
    {
        return $this->belongsTo('App\Models\Area','area_id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch','branch_id');
    }

    public function awb()
    {
        return $this->hasMany('App\Models\Awb','receiver_id');
    }
}
