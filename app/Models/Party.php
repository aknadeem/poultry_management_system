<?php

namespace App\Models;

use App\Traits\CountryPCRelationTrait;
use Illuminate\Database\Eloquent\Model;
use App\Interfaces\HasCountryProvinceCity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Party extends Model implements HasCountryProvinceCity
{
    protected $table = 'parties';
    use SoftDeletes, HasFactory, CountryPCRelationTrait;

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

    public function division()
    {
        return $this->belongsTo('App\Models\Division', 'customer_division_id', 'id')->withDefault([
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
    /*
    I want return "customer" from Parties table.Currently i using where queries like this.
    $customers = Party::where('is_customer', 1)->get();
    But i need same thing any other module also.So i need write again and again this where queries. Here is the solution. That call Scope. */

    public function scopeCustomer($query, $value)
    {
        return $query->where('is_customer', $value);
    }

    /* Laravel know scope as alias. Now i can get active posts using Party::customer(1)->get(); // 1 is for dynamically iscustomer or not customer */

    public function scopeVendor($query, $value)
    {
        return $query->where('is_vendor', $value);
    }

}
