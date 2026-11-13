<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Password Restore Language Lines
    |--------------------------------------------------------------------------
    */

    'invalid_code'         => 'Введен неверный код восстановления.',
    'password_not_changed' => 'Пароль не был изменен.',
    'email_required'       => 'Поле email является обязательным.',
    'phone_required'       => 'Поле phone является обязательным.',
    'too_many_attempts'    => 'Cлишком много попыток ввода кода восстановления.',
    'sms_message_text'     => 'Код восстановления: :code',

    'validation_attributes' => [
        'password' => 'пароль',
        'code'     => 'проверочный код',
        'phone'    => 'номер телефона',
        'email'    => 'email адрес',
        'captcha'  => 'каптча',
    ],

    'validation_messages' => [
        'password_required'     => 'Заполните поле :attribute',
        'password_between'      => 'Поле :attribute должно содержать от :min до :max символов',
        'password_confirmed'    => 'Пароли не совпадают',
        'code_required'         => 'Заполните поле :attribute',
        'code_numeric'          => 'В поле :attribute требуется вводить только цифры',
        'code_digits'           => 'Требуется ввести 6 цифр',
        'email_required'        => 'Заполните поле :attribute',
        'email_is_valid'        => 'Проверьте, правильно ли введён :attribute',
        'email_exists'          => 'Аккаунт с email :input не зарегистрирован',
        'phone_required'        => 'Заполните поле :attribute',
        'phone_digits_between'  => 'Поле :attribute должно содержать от :min до :max цифр',
        'phone_exists'          => 'Аккаунт с телефоном :input не зарегистрирован',
        'captcha_required'      => 'Заполните поле :attribute',
    ]
];
