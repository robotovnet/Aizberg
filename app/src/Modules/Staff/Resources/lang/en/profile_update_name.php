<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'first_name' => 'First name',
        'last_name'  => 'Last name',
        'whatsapp'   => 'WhatsApp',
        'telegram'   => 'Telegram',
        'max'        => 'Max',
    ],

    'validation_messages' => [
        'first_name_required' => 'Fill in the field :attribute',
        'first_name_between'  => 'The :attribute field must contain from :min to :max characters',
        'last_name_required'  => 'Fill in the field :attribute',
        'last_name_between'   => 'The :attribute field must contain from :min to :max characters',
        'whatsapp_max'        => 'Maximum allowed length of :attribute is :max characters',
        'telegram_max'         => 'Maximum allowed length of :attribute is :max characters',
        'max_max'             => 'Maximum allowed length of :attribute is :max characters',
    ]
];
