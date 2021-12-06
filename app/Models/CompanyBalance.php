<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBalance extends Model
{
    protected $table = 'company_balances';
    use HasFactory;
    protected $dates = ['created_at','updated_at'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'company_id' => 'integer',
        'chicken_purchase_id' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }
}
