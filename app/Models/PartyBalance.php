<?php

namespace App\Models;

use App\Helpers\Constant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PartyBalance extends Model
{
    use SoftDeletes,  HasFactory;

    protected $dates = ['created_at','updated_at', 'transaction_date'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public function getTypeValueAttribute()
    {
        $array = [];
        if($this->amount_type === Constant::AMOUNT_TYPE['ToReceive']){
            $array['val'] = 'ToReceive';
            $array['color'] = 'success';
        }else{
            $array['val'] = 'ToPay';
            $array['color'] = 'danger';
        }
        return $array;
    }

    public function getPaymentStatusValAttribute()
    {
        $array = [];
        if($this->payment_status === Constant::PAYMENT_STATUS['UnPaid']){
            $array['val'] = 'UnPaid';
            $array['color'] = 'danger';
        }else if($this->payment_status === Constant::PAYMENT_STATUS['Pending']){
            $array['val'] = 'Pending';
            $array['color'] = 'warning';
        }else{
            $array['val'] = 'Paid';
            $array['color'] = 'success';
        }
        return $array;
    }


    public function party()
    {
        return $this->belongsTo('App\Models\Party', 'party_id', 'id');
    }

    // public function payments()
    // {
    //     return $this->hasMany('App\Models\PartyBalancePayment', 'party_balance_id', 'id');
    // }
}
