<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use SoftDeletes;
    protected $table = 'expenses';
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
