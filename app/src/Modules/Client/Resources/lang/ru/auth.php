<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    */

    'failed'  => 'Неверный логин или пароль',
    'blocked' => 'Пользователь заблокирован!',

    'validation_attributes' => [
        'phone'    => 'номер телефона',
        'email'    => 'email адрес',
        'password' => 'пароль',
    ],

    'validation_messages' => [
        'phone_required'      => 'Укажите :attribute',
        'phone_is_valid'      => 'Проверьте, правильно ли введён :attribute',
        'email_required'      => 'Укажите :attribute',
        'email_is_valid'      => 'Проверьте, правильно ли введён :attribute',
        'password_required'   => 'Укажите :attribute',
        'password_min_length' => 'Некорректный :attribute. Минимальная длина - :min символов',
        'password_max_length' => 'Некорректный :attribute. Максимальная длина - :max символов',
    ]
];
