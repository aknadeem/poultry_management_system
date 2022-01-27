<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductPurchase extends Model
{
    use SoftDeletes, HasFactory;

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    protected $dates = ['created_at','updated_at','deleted_at', 'purchase_date'];

    protected $casts = [
        'quantity' => 'integer',
        'total_quantity' => 'integer',
        'purchase_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'float',
        'tax_amount' => 'decimal:2',
        'tax_percentage' => 'float',
        'final_price' => 'decimal:2',
    ];



}
