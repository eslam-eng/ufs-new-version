<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierSheetDetail extends Model
{
    use HasFactory;
    protected $fillable = ['sheet_id','awb_id'];

    public function sheet()
    {
        return $this->belongsTo('App\Models\CourierSheet','sheet_id');
    }

    public function awb()
    {
        return $this->belongsTo('App\Models\Awb','awb_id')->with(['company', 'department', 'paymentType', 'branch', 'receiver', 'service', 'status', 'city', 'area', 'user']);
    }
}
