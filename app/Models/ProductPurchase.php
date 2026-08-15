<?php

namespace App\Models;

use App\Models\ProductPurchase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductPurchase extends Model
{
    protected $fillable = [
        'product_category_id',
        'party_company_id',
        'purchase_date',
        'expiry_date',
        'due_date_option',
        'manual_number',
        'total_amount',
        'discount_amount',
        'discount_percentage',
        'other_charges',
        'final_amount',
        'payment_status',
        'warranty_period',
        'purchase_invoice',
        'description',
        'addedby',
        'updatedby',
    ];
    use SoftDeletes, HasFactory;

    const PAYMENT_UNPAID = 1;
    const PAYMENT_PENDING = 2;
    const PAYMENT_PAID = 3;

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    protected $casts = [
        'purchase_date' => 'date',
        'expiry_date' => 'date',
        'quantity' => 'integer',
        'total_quantity' => 'integer',
        'purchase_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'float',
        'tax_amount' => 'decimal:2',
        'tax_percentage' => 'float',
        'final_price' => 'decimal:2',
    ];

    public function getStatusValueAttribute()
    {
        $status = [];
        if($this->payment_status == ProductPurchase::PAYMENT_UNPAID){
            $status['value'] = 'Un paid';
            $status['color_name'] = 'danger';

        }elseif($this->payment_status == ProductPurchase::PAYMENT_PENDING){
            $status = 'Pending';
            $status['color_name'] = 'warning';
        }else{
            $status['value'] = 'Un paid';
            $status['color_name'] = 'success';
        }
        return $status;
    }

    public function company()
    {
        return $this->belongsTo('App\Models\PartyCompany', 'party_company_id', 'id')->withDefault([
            'id' => null,
        ]);
    }

    public function detail()
    {
        return $this->hasMany('App\Models\ProductPurchaseDetail', 'product_purchase_id', 'id');
    }

    public function productcategory()
    {
        return $this->belongsTo('App\Models\ProductCategory', 'product_category_id', 'id')->withDefault([
            'id' => null,
        ]);
    }

    public static function boot(){
        parent::boot();
        static::creating(function($model){
            $ex_code = ProductPurchase::max('purchase_number');
            
            if($ex_code >= 99999)
                $length = 6;
            elseif($ex_code >= 999999)
                $length = 7;
            elseif($ex_code >= 9999999)
                $length = 8;
            elseif($ex_code >= 99999999)
                $length = 9;
            else
                $length = 5;

            $model->purchase_number = $ex_code+1;
            $new_number = str_pad($ex_code, $length, 0, STR_PAD_LEFT)+1;
            $model->purchase_code = str_pad($new_number, $length, 0, STR_PAD_LEFT);
        });
    }

}
