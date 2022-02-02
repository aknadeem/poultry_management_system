<?php

namespace App\Helpers;

class Constant
{
    const PAYMENT_STATUS = [
        'UnPaid'   => 1,
        'Pending' => 2,
        'Paid' => 3,
    ];
    
    const VACCINATION_STATUS = [
        'UnPaid'   => 1,
        'Pending' => 2,
        'Paid' => 3,
    ];
    
    const AMOUNT_TYPE = [
        'ToPay'   => 1,
        'ToReceive' => 2,
    ];

    const PAYMENT_METHOD = [
        'BankTransfer'  => 1,
        'Cash' => 2,
        'OnlinePayment' => 3,
        'Other' => 4,
    ];

    const DISCOUNT_PAYMENT = [
        'Pending'  => 1,
        'Done' => 2,
        'Cancel' => 3,
    ];

    const PARTY_TYPE = [
        'Customer'  => 1,
        'Vendor' => 2,
    ];

    const ORDER_STATUS = [
        'New'  => 1,
        'Accept' => 2,
        'Rejected' => 3,
        'Canceled' => 4,
        'Payed' => 5,
        'InProgress' => 6,
        'Delivered' => 7,
        'Received' => 8,
        'InProgress' => 9,
    ];

}