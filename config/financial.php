<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Financial Rollout Flags
    |--------------------------------------------------------------------------
    |
    | Each source entity can be rolled out independently. When a reversal flag
    | is disabled, destructive financial deletes fail closed instead of using
    | the unsafe legacy delete path.
    |
    */
    'rollouts' => [
        'product_purchase' => (bool) env('FINANCIAL_ROLLOUT_PRODUCT_PURCHASE', false),
        'product_sale' => (bool) env('FINANCIAL_ROLLOUT_PRODUCT_SALE', false),
        'chick_purchase' => (bool) env('FINANCIAL_ROLLOUT_CHICK_PURCHASE', false),
        'chicken_purchase' => (bool) env('FINANCIAL_ROLLOUT_CHICKEN_PURCHASE', false),
        'chicken_sale' => (bool) env('FINANCIAL_ROLLOUT_CHICKEN_SALE', false),
        'feed_purchase' => (bool) env('FINANCIAL_ROLLOUT_FEED_PURCHASE', false),
    ],

    'reconciliation' => [
        'notify' => (bool) env('FINANCIAL_RECONCILE_NOTIFY', false),
        'notification_emails' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('FINANCIAL_RECONCILE_EMAILS', ''))
        ))),
    ],
];
