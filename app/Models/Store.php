<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $fillable = ['name','init_value','value'];

    public function makeTransation($value)
    {
        $store = Store::find($this->id);
        $store->increment('value', $value);
    }
}
