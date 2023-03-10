<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pickup extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'date',
        'company_id',
        'status_id',
        'user_id',
        'time_from',
        'time_to',
        'courier_id',
        'notes',
        'shipment_type',
        'shipment_number',
        'trans_type_id'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company','company_id')->select('id', 'name', 'logo');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Status','status_id');
    }

    public function transType()
    {
        return $this->belongsTo('App\Models\TransType','trans_type_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id')->select('id', 'name');
    }

    public function courier()
    {
        return $this->belongsTo('App\Models\Courier','courier_id')->select('id', 'name', 'photo');
    }

    public function pickupHistory()
    {
        return $this->hasMany('App\Models\PickupHistory','pickup_id')->with(['user', 'status']);
    }
}
