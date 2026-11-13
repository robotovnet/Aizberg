<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'password' => 'Пароль',
    ],

    'validation_messages' => [
        'password_required'    => 'Заполните поле :attribute',
        'password_between'     => 'Поле :attribute должно содержать от :min до :max символов',
    ]
];
