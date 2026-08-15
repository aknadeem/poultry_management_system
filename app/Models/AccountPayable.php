<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPayable extends Model
{
    protected $fillable = [
        'amount_type',
        'amount_status',
        'narration',
        'entry_date',
        'model_id',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'dr',
        'cr',
        'addedby',
        'updatedby',
    ];
    use HasFactory;
}
