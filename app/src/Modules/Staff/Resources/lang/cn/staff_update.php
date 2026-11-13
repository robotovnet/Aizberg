<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'first_name'  => '?',
        'last_name'   => '?',
        'email'       => '?',
        'phone'       => '?',
        'groups'      => '?',
        'groups_item' => '?',
        'whatsapp'    => 'WhatsApp',
        'telegram'    => 'Telegram',
        'max'         => 'Max',
    ],

    'validation_messages' => [
        'first_name_required'  => '?',
        'first_name_between'   => '?',
        'last_name_required'   => '?',
        'last_name_between'    => '?',
        'email_required'       => '?',
        'email_email'          => '?',
        'email_max'            => '?',
        'email_unique'         => '?',
        'phone_required'       => '?',
        'phone_digits_between' => '?',
        'phone_unique'         => '?',
        'groups_required'      => '?',
        'groups_item_required' => '?',
        'groups_item_numeric'  => '?',
        'groups_item_exists'   => '?',
        'whatsapp_max'         => '?',
        'telegram_max'         => '?',
        'max_max'              => '?',
    ]
];
