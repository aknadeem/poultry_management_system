<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    public $table = 'expenses';
    use HasFactory;
    protected $dates = ['created_at','updated_at', 'expense_date'];
    protected $casts = [
        'category_id' => 'integer',
        'expense_date' => 'date:Y-m-d',
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\ExpenseCategory', 'category_id', 'id');
    }
    
}
