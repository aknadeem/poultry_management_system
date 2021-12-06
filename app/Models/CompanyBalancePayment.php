<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBalancePayment extends Model
{
    protected $table = 'company_balance_payments';
    use HasFactory;
    protected $dates = ['created_at','updated_at'];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'company_balance_id' => 'integer',
    ];
}
