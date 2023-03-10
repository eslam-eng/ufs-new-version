<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;
    public static $PATH = "/uploads/courier/";
    protected $fillable = [
        'name','photo','phone',	'email','address','notes','active',
        'company_id','insurance_num ','national_id','work_area',
        'branch_id', 'department_id','salary'
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute() {
        return $this->photo? url($this->photo) : '';
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company','company_id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch','branch_id');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department','department_id');
    }

    public function sheets()
    {
        return $this->hasMany('App\Models\CourierSheet','courier_id');
    }

    public function pickupInfo()
    {
        return $this->hasMany('App\Models\Pickup','courier_id');
    }

    public function courierCommission()
    {
        return $this->hasMany('App\Models\CourierCommission','courier_id');
    }

    public function calculateCommission($dateFrom=null, $dateTo=null) {
        $courierSheetQuery = CourierSheet::where('courier_id', $this->id);

        if ($dateFrom)
            $courierSheetQuery->whereDate('date', '>=', $dateFrom);

        if ($dateTo)
            $courierSheetQuery->whereDate('date', '<=', $dateTo);

        $courierSheetIds = $courierSheetQuery->pluck('id')->toArray();
        $awbIds = CourierSheetDetail::whereIn('sheet_id', $courierSheetIds)->pluck('awb_id')->toArray();
        $awbQuery = Awb::whereIn('id', $awbIds);


        if ($dateFrom)
            $awbQuery->whereDate('date', '>=', $dateFrom);

        if ($dateTo)
            $awbQuery->whereDate('date', '<=', $dateTo);

        $courierCommissions = CourierCommission::where('courier_id', $this->id)->get();
        $totalCommission = 0;
        foreach($courierCommissions as $item) {
            $statusClone = clone $awbQuery;
            $totalCommission += $item->commission * $statusClone->where('service_id', $item->service_id)->count();
        }

        return $totalCommission;
    }
}
