<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwbDetail extends Model
{
    use HasFactory;
    protected $fillable = ['awb_id','height','width', 'length'];

    public function awb()
    {
        return $this->belongsTo('App\Models\Awb','awb_id');
    }

}
