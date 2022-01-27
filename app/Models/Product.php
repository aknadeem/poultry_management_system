<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $dates = ['created_at','updated_at','deleted_at', 'reorder_level_date','purchase_date'];

    protected $appends = ['expiry_date_value'];

    protected $casts = [
        'reorder_level_date' => 'date:Y-m-d',
        'mrp_price' => 'decimal:2',
        'whole_sale_price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'float',
        'tax_amount' => 'decimal:2',
        'tax_percentage' => 'float',
        'max_inventory_level' => 'integer',
        'warranty_period' => 'integer',
        'trade_price' => 'decimal:2',
    ];

    public function getExpiryDateValueAttribute()
    {
        $exp_date = today()->addDays($this->warranty_period)->format('Y-m-d');
        return $exp_date;
    }

    public function company()
    {
        return $this->belongsTo('App\Models\PartyCompany', 'party_company_id', 'id')->withDefault(['id' => null]);
    }

    public function category()
    {
        return $this->belongsTo('App\Models\ProductCategory', 'product_category_id', 'id')->withDefault(['id' => null]);
    }

    public function productstore()
    {
        return $this->belongsTo('App\Models\ProductStore', 'product_store_id', 'id')->withDefault(['id' => null]);
    }

    public static function boot(){
        parent::boot();
        static::creating(function($model){
            $ex_code = Product::max('product_code');
            
            if($ex_code >= 99999)
                $length = 6;
            else if($ex_code >= 999999)
                $length = 7;
            else if($ex_code >= 9999999)
                $length = 8;
            else if($ex_code >= 99999999)
                $length = 9;
            else
                $length = 5;

            $new_number = str_pad($ex_code, $length, 0, STR_PAD_LEFT)+1;
            $model->product_code = str_pad($new_number, $length, 0, STR_PAD_LEFT);
            $model->bar_code = str_pad($new_number, $length, 0, STR_PAD_LEFT);
        });
    }
}
