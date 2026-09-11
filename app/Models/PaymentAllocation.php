<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentAllocation extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUS_POSTED = 'posted';

    public const STATUS_VOIDED = 'voided';

    protected $fillable = [
        'payment_type',
        'payment_id',
        'obligation_type',
        'obligation_id',
        'allocated_amount',
        'status',
        'reversal_of_id',
        'idempotency_key',
        'addedby',
        'updatedby',
    ];

    protected function casts(): array
    {
        return [
            'allocated_amount' => 'decimal:2',
            'payment_id' => 'integer',
            'obligation_id' => 'integer',
            'reversal_of_id' => 'integer',
        ];
    }

    public function payment(): MorphTo
    {
        return $this->morphTo();
    }

    public function obligation(): MorphTo
    {
        return $this->morphTo();
    }

    public function reversalOf(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversal_of_id');
    }
}
