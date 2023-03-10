<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceShipment extends Model
{
    use HasFactory;
    protected $fillable = ['name','email','phone','city_to','city_from','desc','weight'];

    public function cityFrom()
    {
        return $this->belongsTo('App\Models\City','city_from');
    }

    public function cityTo()
    {
        return $this->belongsTo('App\Models\City','city_to');
    }
}
