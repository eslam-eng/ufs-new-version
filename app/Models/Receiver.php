<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receiver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'phone', 'company_id', 'city_id', 'area_id',
        'branch_id', 'company_name', 'branch_name', 'address2', 'referance', 'title'
    ];


    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function area(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function branch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function awb(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Awb::class, 'receiver_id');
    }
}
