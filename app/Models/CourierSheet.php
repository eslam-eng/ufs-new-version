<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierSheet extends Model
{
    use HasFactory;

    protected $fillable = ['courier_id','user_id','date'];
    
    protected $appends = [
        'awb_number', 'awb_codes'
    ];
    
    public function getAwbNumberAttribute() {
        return $this->sheetDetails()->count();
    }
    
    public function getAwbCodesAttribute() {
        return implode(", ", $this->sheetDetails()->join('awbs', 'awbs.id', '=', 'awb_id')->pluck('awbs.code')->toArray());
    }

    public function courier()
    {
        return $this->belongsTo('App\Models\Courier','courier_id');
    }
    public  function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }
    public function sheetDetails()
    {
        return $this->hasMany('App\Models\CourierSheetDetail','sheet_id')->with(['awb']);
    }
}
