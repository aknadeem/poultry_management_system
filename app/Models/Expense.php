<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'category_id',
        'amount',
        'expense_date',
        'remarks',
        'picture',
        'addedby',
        'updatedby',
    ];

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

    protected static function booted(): void
    {
        static::created(function (Expense $expense) {
            $expense->updateQuietly([
                'expense_code' => 'EXP-' . str_pad((string) $expense->id, 3,'0',STR_PAD_LEFT),
            ]);
        });
    }
    
}
