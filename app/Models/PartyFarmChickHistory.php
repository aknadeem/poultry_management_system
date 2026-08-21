<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartyFarmChickHistory extends Model
{
    protected $guarded = [];
    use HasFactory;

    public function farm()
    {
        return $this->belongsTo('App\Models\PartyFarm', 'party_farm_id', 'id');
    }
}
