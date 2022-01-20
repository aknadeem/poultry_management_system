<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyBalance extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'company_balances';
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
        return $this->belongsTo('App\Models\PartyCompany', 'company_id', 'id');
    }
    
    public function productpurchase()
    {
        return $this->belongsTo('App\Models\ProductPurchase', 'model_id', 'id')->withDefault(['id' => 0]);
    }

    public function feed()
    {
        return $this->belongsTo('App\Models\Feed', 'model_id', 'id')->where('type', '=', 'feed')->withDefault(['id' => 0]);
    }
}
