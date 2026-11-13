<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'minimum_order_value'  => 'Minimum order amount',
        'maximum_order_value' => 'Maximum order amount',
    ],

    'validation_messages' => [
        'rates_minimum_order_value_min' => ':attribute must be at least 1',
        'rates_maximum_order_value_gt' => ':attribute must be no more than the minimum order amount',
    ]
];
