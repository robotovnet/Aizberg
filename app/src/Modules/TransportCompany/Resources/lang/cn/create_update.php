<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'                         => '?',
        'names_lang'                    => '?',
        'additional_comments'           => '?',
        'code'                          => '?',
        'accept_orders_start_time'      => '?',
        'accept_orders_completion_time' => '?',
        'minimum_order_value_courier'   => '?',
        'minimum_order_value_pickup'    => '?',
        'is_support_courier_delivery'   => '?',
        'is_support_postamats'          => '?',
        'is_support_pickup_points'      => '?',
        'is_active'                     => '?',
    ],

    'validation_messages' => [
        'names_required'                            => '?',
        'additional_comments_required'              => '?',
        'code_required'                             => '?',
        'code_unique'                               => '?',
        'code_between'                              => '?',
        'accept_orders_start_time_required'         => '?',
        'accept_orders_start_time_date_format'      => '?',
        'accept_orders_completion_time_required'    => '?',
        'accept_orders_completion_time_date_format' => '?',
        'minimum_order_value_required'              => '?',
        'minimum_order_value_numeric'               => '?',
        'minimum_order_value_min'                   => '?',
        'is_support_courier_delivery_required'      => '?',
        'is_support_courier_delivery_in'            => '?',
        'is_support_postamats_required'             => '?',
        'is_support_postamats_in'                   => '?',
        'is_support_pickup_points_required'         => '?',
        'is_support_pickup_points_in'               => '?',
        'is_active_required'                        => '?',
        'is_active_in'                              => '?',
        'names_lang_required'                       => '?',
        'names_lang_size'                           => '?',
        'names_lang_exists'                         => '?',
        'names_value_required'                      => '?',
        'names_value_between'                       => '?',
        'additional_comments_value_between'         => '?',
    ]
];
