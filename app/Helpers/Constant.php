<?php

namespace App\Helpers;

class Constant
{
    const PAYMENT_STATUS = [
        'UnPaid'   => 1,
        'Pending' => 2,
        'Paid' => 3,
        'Upcoming' => 4,
        'Overdue' => 5,
    ];

    const PRODUCT_GROUP = [
        'Feed'   => 1,
        'Medicine' => 2,
        'Chicks' => 3,
        'Other' => 4,
    ];

    const PRODUCT_GROUP_VAL = [
        1 => 'Feed',
        2 => 'Medicine',
        3 => 'Chicks',
        4 => 'Other',
    ];

    const PRODUCT_GROUP_COLOR = [
        1 => 'primary',
        2 => 'danger',
        3 => 'success',
        4 => 'secondary',
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

    // const BALANCE_FOR = [
    //     'Chick Purchase'  => 1,
    //     'Chick Sale' => 2,
    //     'Chicken Sale' => 3,
    //     'Product Purchase' => 4,
    //     'Product Sale' => 5,
    //     'Other' => 6,
    // ];

}