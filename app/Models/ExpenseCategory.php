<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $table = 'expense_categories';
    use HasFactory;
    // protected $dates = ['created_at','updated_at'];
}
