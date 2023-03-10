<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierCommission extends Model
{
    use HasFactory;
    protected $fillable = ['courier_id','service_id','commission'];

    public function courier()
    {
        return $this->belongsTo('App\Models\Courier','courier_id');
    }

    public function service()
    {
        return $this->belongsTo('App\Models\Service','service_id');
    }

}
