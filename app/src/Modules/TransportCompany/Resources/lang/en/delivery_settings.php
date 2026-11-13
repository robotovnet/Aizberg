<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'days_transfer'                  => 'Number of days required for delivery of the goods to the delivery city',
        'transfer_time_tomorrow'         => 'The time when the transfer of goods to the transport company is postponed to tomorrow',
        'transfer_time_after_tomorrow'   => 'The time when the transfer of goods to the transport company is postponed to the day after tomorrow',
        'transport_company_working_days' => 'Days of delivery of the goods to the transport company',
        'transport_company_weekends'     => 'Weekend in a transport company',
        'delivery_type'                  => 'Delivery type',
        'delivery_point_id'              => 'ID of the delivery object',
        'transport_company_id'           => 'Transport company',
    ],

    'validation_messages' => [
        'days_transfer_required'                    => 'Fill in the field :attribute',
        'transfer_time_tomorrow_required'           => 'Fill in the field :attribute',
        'transfer_time_after_tomorrow_required'     => 'Fill in the field :attribute',
        'transport_company_working_days_required'   => 'Fill in the field :attribute',
        'transport_company_weekends_required'       => 'Fill in the field :attribute',
        'delivery_type_required'                    => 'Fill in the field :attribute',
        'transport_company_id_required'             => 'Fill in the field :attribute',
        'days_transfer_numeric'                     => 'The :attribute field must contain a number',
        'days_transfer_min'                         => 'The :attribute field must contain a number equal to or greater than zero',
        'transfer_time_tomorrow_date_format'        => 'The time in the field :attribute must be entered in the format: HH:MM',
        'atransfer_time_after_tomorrow_date_format' => 'The time in the field :attribute must be entered in the format: HH:MM',
        'delivery_type_in'                          => 'Supported delivery types: postamat, courier_ity or pickup_point',
        'delivery_point_id_numeric'                 => 'The delivery object ID must be a number',
        'delivery_point_id_min'                     => 'The delivery object ID must be a number equal to or greater than one',
        'transport_company_id_exists'               => 'Transport company with ID = :input does not exist in the system',
        'transport_company_id_numeric'              => 'The transport company ID must be a number',
        'transport_company_id_min'                  => 'The transport company ID must be a number equal to or greater than one',
        'days_names'                                => 'The following values of the names of the days are accepted: Mn, Tu, Wd, Th, Fr, Sa, Sn',
    ]
];
