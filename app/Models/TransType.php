<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransType extends Model
{
    use HasFactory;
    protected $table = "trans_type";
    protected $fillable = ['name'];

}
