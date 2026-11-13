<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'days_transfer'                  => '?',
        'transfer_time_tomorrow'         => '?',
        'transfer_time_after_tomorrow'   => '?',
        'transport_company_working_days' => '?',
        'transport_company_weekends'     => '?',
        'delivery_type'                  => '?',
        'delivery_point_id'              => '?',
        'transport_company_id'           => '?',
    ],

    'validation_messages' => [
        'days_transfer_required'                    => '?',
        'transfer_time_tomorrow_required'           => '?',
        'transfer_time_after_tomorrow_required'     => '?',
        'transport_company_working_days_required'   => '?',
        'transport_company_weekends_required'       => '?',
        'delivery_type_required'                    => '?',
        'transport_company_id_required'             => '?',
        'days_transfer_numeric'                     => '?',
        'days_transfer_min'                         => '?',
        'transfer_time_tomorrow_date_format'        => '?',
        'atransfer_time_after_tomorrow_date_format' => '?',
        'delivery_type_in'                          => '?',
        'delivery_point_id_numeric'                 => '?',
        'delivery_point_id_min'                     => '?',
        'transport_company_id_exists'               => '?',
        'transport_company_id_numeric'              => '?',
        'transport_company_id_min'                  => '?',
        'days_names'                                => '?',
    ]
];
