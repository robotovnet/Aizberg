<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'email' => 'Email адрес',
        'code'  => 'Код',
    ],

    'validation_messages' => [
        'email_required' => 'Заполните поле :attribute',
        'email_email'    => 'Вы ввели некорректный :attribute',
        'email_max'      => 'Максимально допустимая длина e-mail - :max символов',
        'email_unique'   => ':attribute :input уже используется в системе для другого сотрудника',
        'code_numeric'   => 'Код должен состоять только из цифр',
        'code_digits'    => 'Код должен состоять ровно из 6 цифр',
    ]
];
