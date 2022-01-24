<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductSale extends Model
{
    use HasFactory, SoftDeletes;

    const PAYMENT_UNPAID = 1;
    const PAYMENT_PENDING = 2;
    const PAYMENT_PAID = 3;

    public function party()
    {
        return $this->belongsTo('App\Models\Party', 'party_id', 'id')->customer(1)->withDefault([
            'id' => null,
        ]);
    }

    public function company()
    {
        return $this->belongsTo('App\Models\PartyCompany', 'party_company_id', 'id')->withDefault([
            'id' => null,
        ]);
    }
    
    public function productcategory()
    {
        return $this->belongsTo('App\Models\ProductCategory', 'product_category_id', 'id')->withDefault([
            'id' => null,
        ]);
    }

    public function getStatusValueAttribute()
    {
        $status = [];
        if($this->payment_status == ProductSale::PAYMENT_UNPAID){
            $status['value'] = 'Un paid';
            $status['class_name'] = 'text-danger';

        }else if($this->payment_status == ProductSale::PAYMENT_PENDING){
            $status = 'Pending';
            $status['class_name'] = 'text-warning';
        }else{
            $status['value'] = 'Un paid';
            $status['class_name'] = 'text-success';
        }
        return $status;
    }

    public static function boot(){
        parent::boot();
        static::creating(function($model){
            $ex_code = ProductSale::max('sale_number');
            if($ex_code >= 99999)
                $length = 6;
            else if($ex_code >= 999999)
                $length = 7;
            else if($ex_code >= 9999999)
                $length = 8;
            else if($ex_code >= 99999999)
                $length = 9;
            else if($ex_code >= 999999999)
                $length = 10;
            else if($ex_code >= 9999999999)
                $length = 11;
            else if($ex_code >= 99999999999)
                $length = 12;
            else if($ex_code >= 999999999999)
                $length = 13;
            else
                $length = 5;

            $model->sale_number = $ex_code + 1;
            $new_number = str_pad($ex_code, $length, 0, STR_PAD_LEFT)+1;
            $model->sale_code = str_pad($new_number, $length, 0, STR_PAD_LEFT);
        });
    }
}