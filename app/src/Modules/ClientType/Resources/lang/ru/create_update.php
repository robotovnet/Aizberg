<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'         => 'Название типа клиентов',
        'names_lang'    => 'Код языка',
        'is_default'    => 'Тип по умолчанию',
        'price_type_id' => 'Тип цены',
    ],

    'validation_messages' => [
        'names_required'         => 'Заполните поле :attribute',
        'is_default_required'    => 'Заполните поле :attribute',
        'is_default_in'          => 'Допустимые значения поля: "Y" или "N"',
        'price_type_id_required' => 'Выберите :attribute',
        'price_type_id_numeric'  => 'ID может быть только числом',
        'price_type_id_exists'   => ':attribute с ID :input не существует в базе данных',
        'names_lang_required'    => 'Заполните поле :attribute',
        'names_lang_size'        => 'Допускаются только двухбуквенные языковые коды',
        'names_lang_exists'      => 'Язык с кодом :input не существует в нашей системе',
        'names_value_required'   => 'Заполните поле :attribute',
        'names_value_between'    => 'Название типа клиентов должно содержать от :min до :max символов',
    ]
];
