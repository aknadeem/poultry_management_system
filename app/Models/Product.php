<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $casts = [
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\PartyCompany', 'party_company_id', 'id')->withDefault(['id' => null]);
    }

    public function category()
    {
        return $this->belongsTo('App\Models\ProductCategory', 'product_category_id', 'id')->withDefault(['id' => null]);
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
