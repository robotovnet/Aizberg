<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'current_password' => 'Текущий пароль',
        'password'         => 'Новый пароль',
    ],

    'validation_messages' => [
        'current_password_required' => 'Заполните поле :attribute',
        'current_password_between'  => 'Поле :attribute должно содержать от :min до :max символов',
        'password_required'         => 'Заполните поле :attribute',
        'password_between'          => 'Поле :attribute должно содержать от :min до :max символов',
        'password_confirmed'        => 'Пароли не совпадают',
    ]
];
