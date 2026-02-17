<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Late Return Fine Amount
    |--------------------------------------------------------------------------
    |
    | The fine amount (in IDR) charged for late payment of monthly fees.
    |
    */
    'fine' => env('APP_FINE_AMOUNT', 5000),
];
