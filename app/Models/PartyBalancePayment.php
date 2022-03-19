<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartyBalancePayment extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'party_balance_payments';
    protected $dates = ['created_at','updated_at'];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'party_balance_id' => 'integer',
        'party_id' => 'integer',
    ];

    public function party()
    {
        return $this->belongsTo('App\Models\Party', 'party_id', 'id')->withDefault(['id' => null]);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'addedby', 'id')->withDefault(['id' => null]);
    }
}
