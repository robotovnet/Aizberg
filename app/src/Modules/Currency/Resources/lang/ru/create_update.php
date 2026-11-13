<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'         => 'Название валюты',
        'names_lang'    => 'Код языка',
        'country_code'  => 'Страна',
        'is_default'    => 'Валюта по умолчанию',
        'code'          => 'Код валюты',
        'symbol'        => 'Знак валюты',
        'extra_charge'  => 'Наценка к курсу',
    ],

    'validation_messages' => [
        'names_required'        => 'Заполните поле :attribute',
        'is_default_required'   => 'Заполните поле :attribute',
        'is_default_in'         => 'Допустимые значения поля: "Y" или "N"',
        'names_lang_required'   => 'Заполните поле :attribute',
        'names_lang_size'       => 'Допускаются только двухбуквенные языковые коды',
        'names_lang_exists'     => 'Язык с кодом :input не существует в нашей системе',
        'names_value_required'  => 'Заполните поле :attribute',
        'names_value_between'   => 'Название валюты должно содержать от :min до :max символов',
        'code_required'         => 'Заполните поле :attribute',
        'code_size'             => ':attribute обязательно должен быть трехсимвольным',
        'symbol_required'       => 'Заполните поле :attribute',
        'symbol_between'        => ':attribute должен содержать не более :max символов',
        'extra_charge_required' => 'Заполните поле :attribute',
        'extra_charge_numeric'  => 'Поле :attribute должно содержать число',
        'extra_charge_between'  => 'Значение поля должно быть в диапазоне от :min до :max',
        'country_code_required' => 'Выберите страну',
        'country_code_exists'   => 'Страна с кодом :input не найдена в системе',
        'country_code_unique'   => 'Вы уже ранее добавили валюту для страны с кодом :input',
    ]
];
