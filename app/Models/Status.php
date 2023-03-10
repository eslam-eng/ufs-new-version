<?php

namespace App\Models;

use App\Helper\StatusCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;
    protected $fillable = [
        'name','description',
        'steper', 'is_final',
        'sms', 'type', 'code'
        ];
    //  'is_paid_return', 'is_non_paid_return', 'is_customer_paid', 'is_closed',

    public function pickupInfo()
    {
        return $this->hasMany('App\Models\Pickup','status_id');
    }

    public function awb()
    {
        return $this->hasMany('App\Models\Awb','status_id');
    }

    public function awbHistory()
    {
        return $this->hasMany('App\Models\AwbHistory','status_id');
    }

    public static function delivered() {
        $deliveredCode = StatusCode::$DELIVERED;
        return Status::where('code', $deliveredCode)->first();
    }
}

