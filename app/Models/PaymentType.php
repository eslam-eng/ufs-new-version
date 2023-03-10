<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function awb()
    {
        return $this->hasMany('App\Models\Awb','payment_type_id');
    }
}
