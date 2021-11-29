<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public $table = 'employees';
    use HasFactory;
    protected $dates = ['created_at','updated_at', 'date_of_birth'];
    protected $casts = [
        'date_of_birth' => 'date:Y-m-d',
    ];
}
