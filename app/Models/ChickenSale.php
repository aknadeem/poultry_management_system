<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChickenSale extends Model
{
    use SoftDeletes;
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
        // 'manual_number' => 'string',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Party', 'party_id', 'id')->where('is_customer', 1)->withDefault([
            'id' => null
        ]);
    }

}
