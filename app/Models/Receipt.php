<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;
    protected $fillable = [
        'date','store_id','model_id','model_type',
	    'expense_type_id','notes','value', 'type'
    ];


    public function expenseType()
    {
        return $this->belongsTo('App\Models\ExpenseType','expense_type_id');
    }

    public function store()
    {
        return $this->belongsTo('App\Models\Store','store_id');
    }


    public function company()
    {

        return $this->belongsTo('App\Models\Company','model_id');

    }
}
