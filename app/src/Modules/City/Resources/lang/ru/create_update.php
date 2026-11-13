<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'        => 'Название города',
        'names_lang'   => 'Код языка',
        'is_default'   => 'Город по умолчанию',
        'timezone'     => 'Часовой пояс',
        'country_code' => 'Страна',
    ],

    'validation_messages' => [
        'names_required'        => 'Заполните поле :attribute',
        'timezone_required'     => 'Заполните поле :attribute',
        'timezone_timezone'     => 'Некорректный часовой пояс',
        'is_default_required'   => 'Заполните поле :attribute',
        'is_default_in'         => 'Допустимые значения поля: "Y" или "N"',
        'country_code_required' => 'Заполните поле :attribute',
        'country_code_exists'   => 'Страна с кодом :input не существует в нашей системе',
        'names_lang_required'   => 'Заполните поле :attribute',
        'names_lang_size'       => 'Допускаются только двухбуквенные языковые коды',
        'names_lang_exists'     => 'Язык с кодом :input не существует в нашей системе',
        'names_value_required'  => 'Заполните поле :attribute',
        'names_value_between'   => 'Название города должно содержать от :min до :max символов',
    ]
];
