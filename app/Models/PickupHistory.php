<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupHistory extends Model
{
    use HasFactory;

    protected $fillable = ['pickup_id','user_id','status_id'];


    public function pickup()
    {
        return $this->belongsTo('App\Models\Pickup','pickup_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id')->select('id', 'name');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Status','status_id');
    }

}
