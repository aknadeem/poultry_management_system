<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartyBalancePayment extends Model
{
    use SoftDeletes, HasFactory;

    protected $guarded = [];

    protected $table = 'party_balance_payments';

    protected $dates = ['created_at', 'updated_at'];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'party_balance_id' => 'integer',
        'party_id' => 'integer',
        'reversed_at' => 'datetime',
        'reversal_of_id' => 'integer',
        'reversed_by' => 'integer',
    ];

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'party_id', 'id')->withDefault(['id' => null]);
    }

    public function partyBalance(): BelongsTo
    {
        return $this->belongsTo(PartyBalance::class, 'party_balance_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'addedby', 'id')->withDefault(['id' => null]);
    }

    public function allocations(): MorphMany
    {
        return $this->morphMany(PaymentAllocation::class, 'payment');
    }

    public function financialTransactions(): MorphMany
    {
        return $this->morphMany(FinancialTransaction::class, 'reference');
    }

    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversal_of_id');
    }
}
