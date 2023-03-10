<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierDaily extends Model
{
    use HasFactory;
    protected $fillable = ['courier_id', 'discount', 'date', 'additional', 'commission', 'salary', 'notes', 'discount_expense_id', 'additional_expense_id'];

    public function courier()
    {
        return $this->belongsTo('App\Models\Courier','courier_id');
    }

    public function discountExpense() {
        return $this->belongsTo(ExpenseType::class, "discount_expense_id");
    }

    public function additionalExpense() {
        return $this->belongsTo(ExpenseType::class, "additional_expense_id");
    }
}
