<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'      => 'название типа цены',
        'names_lang' => 'код языка',
        'code'       => 'код',
    ],

    'validation_messages' => [
        'names_required'       => 'Заполните поле :attribute',
        'code_required'        => 'Заполните поле :attribute',
        'code_max'             => 'Код должен состоять не более чем из :max символов',
        'code_unique'          => 'Вы уже ранее добавили тип цены с кодом :input',
        'names_lang_required'  => 'Заполните поле :attribute',
        'names_lang_size'      => 'Допускаются только двухбуквенные языковые коды',
        'names_lang_exists'    => 'Язык с кодом :input не существует в нашей системе',
        'names_value_required' => 'Заполните поле :attribute',
        'names_value_between'  => 'Название типа цены должно содержать от :min до :max символов',
    ]
];
