<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLevel extends Model
{
    protected $table = 'user_levels';
    use HasFactory;
    protected $dates = ['created_at','updated_at'];
}
