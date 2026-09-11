<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyBalance extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'company_id',
        'type',
        'model_id',
        'reference_type',
        'reference_id',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'dr',
        'cr',
        'status',
        'financial_status',
        'balance_type',
        'addedby',
        'updatedby',
    ];

    protected $table = 'company_balances';

    protected $dates = ['created_at', 'updated_at'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'company_id' => 'integer',
        'chicken_purchase_id' => 'integer',
        'reference_id' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(PartyCompany::class, 'company_id', 'id');
    }

    public function productpurchase(): BelongsTo
    {
        return $this->belongsTo(ProductPurchase::class, 'model_id', 'id')->withDefault(['id' => 0]);
    }

    public function feed(): BelongsTo
    {
        return $this->belongsTo(Feed::class, 'model_id', 'id')->where('type', '=', 'feed')->withDefault(['id' => 0]);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CompanyBalancePayment::class, 'company_balance_id', 'id');
    }

    public function allocations(): MorphMany
    {
        return $this->morphMany(PaymentAllocation::class, 'obligation');
    }
}
