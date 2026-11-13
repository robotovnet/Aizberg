<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'product_id'     => 'Id продукта',
        'client_id'      => 'Id клиента',
        'staff_id'       => 'Id сотрудника',
        'reply_to_qid'   => 'Id вопроса',
        'replied'        => 'Флаг ответа',
        'content'        => 'Текст вопроса/ответа',
        'reply_to_mail'  => 'Отправить ответ на email',
    ],

    'validation_messages' => [
        'product_id_required' => 'Заполните поле :attribute',
        'client_id_required' => 'Заполните поле :attribute',
        'staff_id_required' => 'Заполните поле :attribute',
    ]
];
