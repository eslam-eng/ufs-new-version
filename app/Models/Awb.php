<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Awb extends Model
{
    use HasFactory,SoftDeletes;
    use Filterable;
    public static $DEFAULIMAGE =  '/uploads/images/avatar.png';

    protected $fillable = [
        'code', 'company_id', 'department_id',
        'branch_id', 'receiver_id', 'payment_type_id', 'service_id', 'status_id', 'user_id', 'city_id',
        'area_id','receiver_name','receiver_title', 'attachment','id_number','date', 'weight', 'pieces',
        'category_id', 'notes', 'is_return', 'collection', //now its temporary after it will calc automaticlly depends on city an area price
        'zprice', 'shiping_price', 'additional_kg_price', 'additional_price', 'net_price', 'created_at',
        'rent', 'clearance', 'custom_dec', 'delay'
    ];

    protected $appends =['sheet_id', 'receiver_city', 'receiver_area', 'city', 'area'];

    public function getAttachmentAttribute($value) {
        if (isset($value) && file_exists( public_path().'/uploads/awbs/delivered/'.$value ))
            return asset('uploads/awbs/delivered/'.$value);
        return url(self::$DEFAULIMAGE);
    }

    public function details()
    {
        return $this->hasMany(AwbDetail::class,'awb_id');
    }

    public function courierSheet()
    {
        return $this->hasOne(CourierSheetDetail::class,'awb_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class,'department_id')->select('id', 'name');
    }

    public function receiver()
    {
        return $this->belongsTo(Receiver::class,'receiver_id');
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class,'payment_type_id')->select('id', 'name');
    }

    public function service()
    {
        return $this->belongsTo(Service::class,'service_id')->select('id', 'name');
    }

    public function status()
    {
        return $this->belongsTo(Status::class,'status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id')->select('id', 'name');
    }

    public function city()
    {
        return $this->belongsTo(City::class,'city_id')->select('id', 'name');
    }

    public function area()
    {
        return $this->belongsTo(Area::class,'area_id')->select('id', 'name');
    }

    public function awbHistory()
    {
        return $this->hasMany(AwbHistory::class,'awb_id')->with(['status', 'user']);
    }

    public function awbCategory(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AwbCategory::class,'category_id');
    }

    public function scopeSearch(Builder $query, string $term = null)
    {
        $term = '%'.$term.'%';
        $query->where('code', 'like', $term)
            ->orWhereIntegerInRaw('receiver_id', Receiver::query()
                ->select('id')
                ->where('referance', 'like', $term)
                ->orWhere('phone','like', $term)
                ->limit(3)
                ->get()
                ->pluck('id')
            )->limit(3);
    }
}
