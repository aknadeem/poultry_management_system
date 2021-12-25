<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerBalance extends Model
{
    use SoftDeletes;
    protected $table = 'customer_balances';
    use HasFactory;

    protected $dates = ['created_at','updated_at', 'balance_date'];

    protected $casts = [
        'balance_date' => 'date:Y-m-d',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'payable_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\CustomerPayment', 'balance_id', 'id');
    }

}
