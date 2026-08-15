<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedPurchase extends Model
{
    protected $fillable = [
        'status',
        'payment_status',
        'feed_id',
        'company_id',
        'purchase_date',
        'quantity',
        'remaining_quantity',
        'price',
        'discount_amount',
        'per_bag_discount_amount',
        'discount_percentage',
        'total_price',
        'final_price',
        'bilty_number',
        'bilty_charges',
        'sale_order_number',
        'delivery_order_number',
        'description',
        'picture',
        'addedby',
        'updatedby',
    ];
    use HasFactory;

    public function company()
    {
        return $this->belongsTo('App\Models\PartyCompany', 'company_id', 'id');
    }

    protected $casts = [
        'purchase_date' => 'date',
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];
}
