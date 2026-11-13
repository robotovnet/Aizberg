<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'first_name' => '?',
        'last_name'  => '?',
        'whatsapp'   => 'WhatsApp',
        'telegram'   => 'Telegram',
        'max'        => 'Max',
    ],

    'validation_messages' => [
        'first_name_required' => '?',
        'first_name_between'  => '?',
        'last_name_required'  => '?',
        'last_name_between'   => '?',
        'whatsapp_max'        => '?',
        'telegram_max'         => '?',
        'max_max'             => '?',
    ]
];
