<?php

namespace App\Models;

use App\Models\ProductPurchase;
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

    public function company()
    {
        return $this->belongsTo('App\Models\PartyCompany', 'party_company_id', 'id')->withDefault([
            'id' => null,
        ]);
    }

    public static function boot(){
        parent::boot();
        static::creating(function($model){
            $ex_code = ProductPurchase::max('purchase_number');
            
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

            $model->purchase_number = $ex_code+1;
            $new_number = str_pad($ex_code, $length, 0, STR_PAD_LEFT)+1;
            $model->purchase_code = str_pad($new_number, $length, 0, STR_PAD_LEFT);
        });
    }

}
