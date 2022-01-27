<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartyFarm extends Model
{
    use SoftDeletes, HasFactory;

    public function party()
    {
        return $this->belongsTo('App\Models\Party', 'party_id', 'id');
    }
    public function type()
    {
        return $this->belongsTo('App\Models\FarmType', 'farm_type_id', 'id');
    }
    public function subtype()
    {
        return $this->belongsTo('App\Models\FarmSubtype', 'farm_subtype_id', 'id');
    }
}
