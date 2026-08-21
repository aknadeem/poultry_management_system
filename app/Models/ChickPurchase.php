<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChickPurchase extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'purchase_status',
        'chick_grade_id',
        'bill_no',
        'purchase_date',
        'vendor_id',
        'company_id',
        'customer_id',
        'party_farm_id',
        'chick_entry_age',
        'chick_current_age',
        'weight',
        'quantity',
        'price',
        'discount_amount',
        'discount_percentage',
        'total_price',
        'final_price',
        'bilty_number',
        'bilty_charges',
        'sale_order_number',
        'delivery_order_number',
        'vehicle_number',
        'driver_name',
        'driver_contact',
        'description',
        'picture',
        'addedby',
        'updatedby',
    ];

    protected $table = 'chick_purchases';
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

    public function customer()
    {
        return $this->belongsTo('App\Models\Party', 'customer_id', 'id')->withDefault([
            'id' => null,
        ]);
    }

    public function grade()
    {
        return $this->belongsTo('App\Models\ChickGrade', 'chick_grade_id', 'id')->withDefault([
            'id' => null,
        ]);
    }

    public function farmHistory()
    {
        return $this->hasOne('App\Models\PartyFarmChickHistory', 'chick_purchase_id', 'id');
    }
}
