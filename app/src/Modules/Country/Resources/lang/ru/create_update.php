<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'         => 'Название страны',
        'names_lang'    => 'Код языка',
        'is_default'    => 'Страна по умолчанию',
        'language'      => 'Язык страны',
        'code'          => 'Код страны',
    ],

    'validation_messages' => [
        'names_required'         => 'Заполните поле :attribute',
        'is_default_required'    => 'Заполните поле :attribute',
        'is_default_in'          => 'Допустимые значения поля: "Y" или "N"',
        'language_required'      => 'Выберите :attribute',
        'language_size'          => 'Допускаются только двухбуквенные языковые коды',
        'language_exists'        => ':attribute с ID :input не существует в нашей системе',
        'code_required'          => 'Заполните поле :attribute',
        'code_size'              => 'Допускаются только двухбуквенные коды стран',
        'names_lang_required'    => 'Заполните поле :attribute',
        'names_lang_size'        => 'Допускаются только двухбуквенные языковые коды',
        'names_lang_exists'      => 'Язык с кодом :input не существует в нашей системе',
        'names_value_required'   => 'Заполните поле :attribute',
        'names_value_between'    => 'Название страны должно содержать от :min до :max символов',
    ]
];
