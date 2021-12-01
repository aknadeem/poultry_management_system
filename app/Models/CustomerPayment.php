<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    protected $table = 'customer_payments';
    use HasFactory;

    protected $dates = ['created_at','updated_at', 'paid_date'];
    
    protected $casts = [
        'paid_date' => 'date:Y-m-d',
        'paid_amount' => 'decimal:2',
        'balance_id' => 'integer',
        'customer_id' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id', 'id');
    }

    public function balance()
    {
        return $this->belongsTo('App\Models\CustomerBalance', 'balance_id', 'id');
    }
}
