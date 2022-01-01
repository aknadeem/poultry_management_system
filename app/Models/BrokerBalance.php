<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BrokerBalance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'broker_balances';
    protected $dates = ['created_at','updated_at'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'broker_id' => 'integer',
        'dr' => 'decimal:2',
        'cr' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function broker()
    {
        return $this->belongsTo('App\Models\Broker', 'broker_id', 'id');
    }
}
