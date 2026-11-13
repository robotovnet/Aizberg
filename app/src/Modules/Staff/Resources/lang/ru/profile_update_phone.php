<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'phone' => 'Номер телефона',
        'code'  => 'Код',
    ],

    'validation_messages' => [
        'phone_required'       => 'Заполните поле :attribute',
        'phone_digits_between' => ':attribute должен содержать от :min до :max цифр в номере',
        'phone_unique'         => ':attribute :input уже используется в системе для другого сотрудника',
        'code_numeric'         => 'Код должен состоять только из цифр',
        'code_digits'          => 'Код должен состоять ровно из 6 цифр',
    ]
];
