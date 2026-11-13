<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'                         => 'Name of the transport company',
        'names_lang'                    => 'Language code',
        'additional_comments'           => 'Additional comment',
        'code'                          => 'Code of the transport company',
        'accept_orders_start_time'      => 'Start of acceptance orders (time)',
        'accept_orders_completion_time' => 'Completion of acceptance orders (time)',
        'minimum_order_value_courier'   => 'Minimum order amount (courier delivery)',
        'minimum_order_value_pickup'    => 'Minimum order amount (pickup points and parcel lockers)',
        'is_support_courier_delivery'   => 'Courier delivery support',
        'is_support_postamats'          => 'Postamats support',
        'is_support_pickup_points'      => 'Pickup points support',
        'is_active'                     => 'Is active',
    ],

    'validation_messages' => [
        'names_required'                            => 'Fill in the field :attribute',
        'additional_comments_required'              => 'Fill in the field :attribute',
        'code_required'                             => 'Fill in the field :attribute',
        'code_unique'                               => 'You have already added a transport company with the code :input',
        'code_between'                              => 'The :attribute field must contain from :min to :max characters',
        'accept_orders_start_time_required'         => 'Fill in the field :attribute',
        'accept_orders_start_time_date_format'      => 'Invalid format of the order acceptance start time',
        'accept_orders_completion_time_required'    => 'Fill in the field :attribute',
        'accept_orders_completion_time_date_format' => 'Invalid format of the order acceptance completion time',
        'minimum_order_value_required'              => 'Fill in the field :attribute',
        'minimum_order_value_numeric'               => ':attribute must be a number',
        'minimum_order_value_min'                   => ':attribute must be a positive number',
        'is_support_courier_delivery_required'      => 'Fill in the field :attribute',
        'is_support_courier_delivery_in'            => 'Valid field values: "Y" or "N"',
        'is_support_postamats_required'             => 'Fill in the field :attribute',
        'is_support_postamats_in'                   => 'Valid field values: "Y" or "N"',
        'is_support_pickup_points_required'         => 'Fill in the field :attribute',
        'is_support_pickup_points_in'               => 'Valid field values: "Y" or "N"',
        'is_active_required'                        => 'Fill in the field :attribute',
        'is_active_in'                              => 'Valid field values: "Y" or "N"',
        'names_lang_required'                       => 'Fill in the field :attribute',
        'names_lang_size'                           => 'Only two-letter language codes are allowed',
        'names_lang_exists'                         => 'The language with the code :input does not exist in our system',
        'names_value_required'                      => 'Fill in the field :attribute',
        'names_value_between'                       => 'The name of the transport company must contain from :min to :max characters',
        'additional_comments_value_between'         => 'The comment must contain from :min to :max characters',
    ]
];
