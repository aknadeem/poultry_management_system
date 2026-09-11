<?php

namespace App\Models;

use App\Helpers\Constant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartyBalance extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'party_id',
        'reference_type',
        'reference_id',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'transaction_date',
        'amount_type',
        'payment_status',
        'financial_status',
        'narration',
        'addedby',
        'updatedby',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'reference_id' => 'integer',
    ];

    public function getTypeValueAttribute()
    {
        $array = [];
        if ($this->amount_type === Constant::AMOUNT_TYPE['ToReceive']) {
            $array['val'] = 'ToReceive';
            $array['color'] = 'success';
        } else {
            $array['val'] = 'ToPay';
            $array['color'] = 'danger';
        }

        return $array;
    }

    public function getPaymentStatusValAttribute()
    {
        $array = [];
        if ($this->payment_status === Constant::PAYMENT_STATUS['UnPaid']) {
            $array['val'] = 'UnPaid';
            $array['color'] = 'danger';
        } elseif ($this->payment_status === Constant::PAYMENT_STATUS['Pending']) {
            $array['val'] = 'Pending';
            $array['color'] = 'warning';
        } else {
            $array['val'] = 'Paid';
            $array['color'] = 'success';
        }

        return $array;
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'party_id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PartyBalancePayment::class, 'party_balance_id', 'id');
    }

    public function allocations(): MorphMany
    {
        return $this->morphMany(PaymentAllocation::class, 'obligation');
    }
}
