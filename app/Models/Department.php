<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'company_id'];

    public function couriers()
    {
        return $this->hasMany('App\Models\Courier','department_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company','company_id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User','department_id');
    }
}
