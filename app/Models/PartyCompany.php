<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartyCompany extends Model
{
    use SoftDeletes, HasFactory;

    public function vendor()
    {
        return $this->belongsTo('App\Models\Party', 'party_id', 'id')->where('is_vendor', 1)->withDefault(['id' => null]);
    }

    public function businesstype()
    {
        return $this->belongsTo('App\Models\BusinessType', 'business_type_id', 'id')->withDefault(['id' => null]);
    }
}
