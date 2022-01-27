<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feed extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'feeds';
    protected $dates = ['created_at','updated_at', 'purchase_date'];
    protected $casts = [
        'purchase_date' => 'date:Y-m-d',
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'float',
        'total_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\FeedCategory', 'feed_category_id', 'id');
    }
    
    public function purchases()
    {
        return $this->hasMany('App\Models\FeedPurchase', 'feed_id', 'id');
    }

}
