<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwbCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name','fixed','has_many'];

    public function awb()
    {
        return $this->hasMany('App\Models\Awb','category_id');
    }
}
