<?php

namespace App\Models;

use App\Traits\CountryPCRelationTrait;
use Illuminate\Database\Eloquent\Model;
use App\Interfaces\HasCountryProvinceCity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Party extends Model implements HasCountryProvinceCity
{
    protected $fillable = [
        'is_vendor',
        'is_customer',
        'is_active',
        'name',
        'guardian_name',
        'cnic_no',
        'email',
        'contact_no',
        'business_no',
        'manual_number',
        'address',
        'customer_type_id',
        'vendor_type_id',
        'customer_division_id',
        'vendor_division_id',
        'description',
        'country_id',
        'province_id',
        'city_id',
        'contact_person_id',
        'balance',
        'balance_type',
        'profile_picture',
        'cnic_front',
        'cnic_back',
        'signature',
        'addedby',
        'updatedby',
    ];

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

    public function balances()
    {
        return $this->hasMany('App\Models\PartyBalance', 'party_id', 'id');
    }

    public function scopeCustomer($query, $value)
    {
        return $query->where('is_customer', $value);
    }

    public function scopeVendor($query, $value)
    {
        return $query->where('is_vendor', $value);
    }

    protected static function booted(): void
    {
        static::created(function (Party $party) {
            $party->updateQuietly([
                'party_code' => 'PTY-' . str_pad((string) $party->id, 3,'0',STR_PAD_LEFT),
            ]);
        });
    }
}
