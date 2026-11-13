<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Registration Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'phone'        => 'номер телефона',
        'email'        => 'email адрес',
        'password'     => 'пароль',
        'first_name'   => 'фамилия',
        'last_name'    => 'имя',
        'timezone'     => 'часовой пояс',
        'city_id'      => 'город',
        'country_code' => 'страна',
        'captcha'      => 'проверочный код',
    ],

    'validation_messages' => [
        'first_name_required'   => 'Заполните поле :attribute',
        'first_name_between'    => 'Поле :attribute должно содержать от :min до :max символов',
        'last_name_required'    => 'Заполните поле :attribute',
        'last_name_between'     => 'Поле :attribute должно содержать от :min до :max символов',
        'email_required'        => 'Заполните поле :attribute',
        'email_is_valid'        => 'Проверьте, правильно ли введён :attribute',
        'email_unique'          => 'Аккаунт с email :input уже зарегистрирован ранее',
        'email_max'             => 'Поле :attribute не должно содержать более :max символов',
        'phone_required'        => 'Заполните поле :attribute',
        'phone_digits_between'  => 'Поле :attribute должно содержать от :min до :max цифр',
        'phone_unique'          => 'Аккаунт с телефоном :input уже зарегистрирован ранее',
        'password_required'     => 'Заполните поле :attribute',
        'password_between'      => 'Поле :attribute должно содержать от :min до :max символов',
        'password_confirmed'    => 'Пароли не совпадают',
        'captcha_required'      => 'Введите проверочный код с картинки',
        'captcha_invalid'       => 'Неверный проверочный код',
    ]
];
