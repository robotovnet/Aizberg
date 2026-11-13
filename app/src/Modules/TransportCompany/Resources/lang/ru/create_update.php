<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'                         => 'Название ТК',
        'names_lang'                    => 'Код языка',
        'additional_comments'           => 'Дополнительный комментарий',
        'code'                          => 'Код ТК',
        'accept_orders_start_time'      => 'Начало приема заказов (время)',
        'accept_orders_completion_time' => 'Завершение приема заказов (время)',
        'minimum_order_value_courier'   => 'Минимальная сумма заказа (курьерская доставка)',
        'minimum_order_value_pickup'    => 'Минимальная сумма заказа (ПВЗ и Постаматы)',
        'is_support_courier_delivery'   => 'Поддержка курьерской доставки',
        'is_support_postamats'          => 'Поддержка постаматов',
        'is_support_pickup_points'      => 'Поддержка ПВЗ',
        'is_active'                     => 'Активировать',
    ],

    'validation_messages' => [
        'names_required'                            => 'Заполните поле :attribute',
        'additional_comments_required'              => 'Заполните поле :attribute',
        'code_required'                             => 'Заполните поле :attribute',
        'code_unique'                               => 'Вы уже ранее добавили тк с кодом :input',
        'code_between'                              => 'Поле :attribute должно содержать от :min до :max символов',
        'accept_orders_start_time_required'         => 'Заполните поле :attribute',
        'accept_orders_start_time_date_format'      => 'Неверный формат времени начала приема заказов',
        'accept_orders_completion_time_required'    => 'Заполните поле :attribute',
        'accept_orders_completion_time_date_format' => 'Неверный формат времени завершения приема заказов',
        'minimum_order_value_required'              => 'Заполните поле :attribute',
        'minimum_order_value_numeric'               => ':attribute должна быть числом',
        'minimum_order_value_min'                   => ':attribute должна быть положительным числом',
        'is_support_courier_delivery_required'      => 'Заполните флаг :attribute',
        'is_support_courier_delivery_in'            => 'Допустимые значения поля: "Y" или "N"',
        'is_support_postamats_required'             => 'Заполните флаг :attribute',
        'is_support_postamats_in'                   => 'Допустимые значения поля: "Y" или "N"',
        'is_support_pickup_points_required'         => 'Заполните флаг :attribute',
        'is_support_pickup_points_in'               => 'Допустимые значения поля: "Y" или "N"',
        'is_active_required'                        => 'Заполните флаг :attribute',
        'is_active_in'                              => 'Допустимые значения поля: "Y" или "N"',
        'names_lang_required'                       => 'Заполните поле :attribute',
        'names_lang_size'                           => 'Допускаются только двухбуквенные языковые коды',
        'names_lang_exists'                         => 'Язык с кодом :input не существует в нашей системе',
        'names_value_required'                      => 'Заполните поле :attribute',
        'names_value_between'                       => 'Название ТК должно содержать от :min до :max символов',
        'additional_comments_value_between'         => 'Комментарий должен содержать от :min до :max символов',
    ]
];
