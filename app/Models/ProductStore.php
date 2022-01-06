<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStore extends Model
{
    use HasFactory;

    protected $casts = [
        'store_code' => 'integer',
    ];

    public static function boot(){
        parent::boot();
        static::creating(function($model){
            $ex_code = ProductStore::max('store_code');
            
            if($ex_code >= 99999)
                $length = 6;
            else if($ex_code >= 999999)
                $length = 7;
            else if($ex_code >= 9999999)
                $length = 8;
            else
                $length = 5;

            $new_number = str_pad($ex_code, $length, 0, STR_PAD_LEFT)+1;
            $model->store_code = str_pad($new_number, $length, 0, STR_PAD_LEFT);
        });
    }
}
