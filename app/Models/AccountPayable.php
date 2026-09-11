<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPayable extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'amount_type',
        'amount_status',
        'narration',
        'entry_date',
        'model_id',
        'reference_type',
        'reference_id',
        'company_balance_id',
        'company_balance_payment_id',
        'legacy_payment_row',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'dr',
        'cr',
        'addedby',
        'updatedby',
    ];

    public function companyBalance(): BelongsTo
    {
        return $this->belongsTo(CompanyBalance::class, 'company_balance_id');
    }

    public function companyBalancePayment(): BelongsTo
    {
        return $this->belongsTo(CompanyBalancePayment::class, 'company_balance_payment_id');
    }

    public function financialTransactions(): MorphMany
    {
        return $this->morphMany(FinancialTransaction::class, 'reference');
    }
}
