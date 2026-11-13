<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'first_name' => 'Имя',
        'last_name'  => 'Фамилия',
        'whatsapp'   => 'WhatsApp',
        'telegram'   => 'Telegram',
        'max'        => 'Max',
    ],

    'validation_messages' => [
        'first_name_required' => 'Заполните поле :attribute',
        'first_name_between'  => 'Поле :attribute должно содержать от :min до :max символов',
        'last_name_required'  => 'Заполните поле :attribute',
        'last_name_between'   => 'Поле :attribute должно содержать от :min до :max символов',
        'whatsapp_max'        => 'Максимально допустимая длина :attribute - :max символов',
        'telegram_max'         => 'Максимально допустимая длина :attribute - :max символов',
        'max_max'             => 'Максимально допустимая длина :attribute - :max символов',
    ]
];
