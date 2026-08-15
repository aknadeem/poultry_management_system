<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChickenPurchase extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'purchase_date',
        'chick_grade_id',
        'company_id',
        'weight',
        'quantity',
        'price',
        'discount_amount',
        'discount_percentage',
        'total_price',
        'bilty_number',
        'bilty_charges',
        'sale_order_number',
        'delivery_order_number',
        'vehicle_number',
        'driver_name',
        'driver_contact',
        'personal_farm_id',
        'picture',
        'addedby',
        'updatedby',
    ];

    protected $table = 'chicken_purchases';
    use HasFactory;
    protected $dates = ['created_at','updated_at', 'purchase_date'];
    protected $casts = [
        'purchase_date' => 'date:d M, Y',
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'float',
        'total_price' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\PartyCompany', 'company_id', 'id');
    }

}
