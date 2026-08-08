<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartyAccount extends Model
{
    protected $guarded = [];
    use SoftDeletes;
    use HasFactory;
}
