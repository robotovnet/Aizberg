<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'subdomain'             => 'Поддомен',
        'city_id'               => 'Город',
        'city_name_dative_case' => 'Город в дательном падеже',
        'phone'                 => 'Контактный телефон',
        'is_active'             => 'Активация магазина',
        'country'               => 'Страна',
    ],

    'validation_messages' => [
        'subdomain_between'              => ':attribute может содержать от :min до :max символов',
        'subdomain_unique'               => 'Вы уже ранее добавили магазин с поддоменом :input',
        'country_id_min'                 => 'Выберите страну',
        'country_id_required'            => 'Выберите страну',
        'city_id_required'               => 'Заполните поле :attribute',
        'city_id_min'                    => 'Выберите город',
        'city_id_numeric'                => ':attribute должен содержать числовой ID',
        'city_id_exists'                 => 'Выбранный город с ID :input не найден в системе',
        'city_name_dative_case_required' => 'Заполните поле :attribute',
        'city_name_dative_case_between'  => ':attribute может содержать от :min до :max символов',
        'phone_required'                 => 'Заполните поле :attribute',
        'phone_digits_between'           => ':attribute может содержать от :min до :max цифр',
        'is_active_required'             => 'Заполните поле :attribute',
        'is_active_in'                   => 'Допустимые значения поля: "Y" или "N"',
    ]
];
