<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Party extends Model
{
    protected $table = 'parties';
    use SoftDeletes, HasFactory;

    protected $dates = ['created_at','updated_at','deleted_at'];
    // protected $casts = [
    //     'quantity' => 'integer',
    //     'price' => 'decimal:2',
    //     'discount_amount' => 'decimal:2',
    //     'discount_percentage' => 'float',
    //     'total_price' => 'decimal:2',
    // ];

    public function farm()
    {
        return $this->hasOne('App\Models\PartyFarm', 'party_id', 'id')->withDefault([
            'id' => null,
        ]);
    }
    
    public function balancelimit()
    {
        return $this->hasOne('App\Models\PartyBalanceLimit', 'party_id', 'id')->where('is_active', 1)->where('start_date', '>=', today())->withDefault([
            'id' => null,
        ]);
    }
    
    public function company()
    {
        return $this->hasOne('App\Models\PartyCompany', 'party_id', 'id')->withDefault([
            'id' => null,
        ]);
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id', 'id');
    }

    public function province()
    {
        return $this->belongsTo('App\Models\Province', 'province_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city_id', 'id');
    }
}
