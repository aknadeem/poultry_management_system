<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyBalancePayment extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'company_balance_payments';
    protected $dates = ['created_at','updated_at'];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'company_balance_id' => 'integer',
        'party_company_id' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\PartyCompany', 'party_company_id', 'id')->withDefault(['id' => null]);
    }

    public function addedBy()
    {
        return $this->belongsTo('App\Models\User', 'addedby', 'id')->withDefault(['id' => null]);
    }
}
