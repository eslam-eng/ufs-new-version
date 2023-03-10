<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    public static $PATH='/uploads/company/';
    protected $fillable = [
        'name','logo','ceo','address','phone','fax','email'	,'active','notes',
        'commercial_number','commercial_photo','type','city_id','area_id', 'show_dashboard', 'more_details'
    ];

    protected $appends = ['logo_url', 'commercial_photo_url'];

    public function getMoreDetailsAttribute() {
        return $this->attributes['more_details']? json_decode($this->attributes['more_details']) : ['active' => 1];
    }

    public function getLogoUrlAttribute() {
        return $this->logo? url($this->logo) : '';
    }

    public function getCommercialPhotoUrlAttribute() {
        return $this->commercial_photo? url($this->commercial_photo) : '';
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City','city_id');
    }

    public function area()
    {
        return $this->belongsTo('App\Models\Area','area_id');
    }

    public function branches()
    {
        return $this->hasMany('App\Models\Branch','company_id');
    }

    public function Couriers()
    {
        return $this->hasMany('App\Models\Courier','company_id');
    }

    public function pickupInfo()
    {
        return $this->hasMany('App\Models\Pickup','company_id');
    }

    public function recivers()
    {
        return $this->hasMany('App\Models\Receiver','company_id');
    }

    public function setting()
    {
        return $this->hasMany('App\Models\Setting','company_id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User','company_id');
    }

    public function awb()
    {
        return $this->hasMany('App\Models\Awb','company_id');
    }

    public static function admin() {
        return Company::find(1);
    }
}
