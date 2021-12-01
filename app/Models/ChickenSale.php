<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChickenSale extends Model
{
    protected $table = 'chicken_sales';
    use HasFactory;
    protected $dates = ['created_at','updated_at', 'sale_date'];
    protected $casts = [
        'sale_date' => 'date:Y-m-d',
        'total_weight' => 'float',
        'per_kg_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'float',
        'total_price' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id', 'id');
    }

}
