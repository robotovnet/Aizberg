<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'minimum_order_value'  => 'Минимальная сумма заказа',
        'maximum_order_value'  => 'Максимальная сумма заказа',
        'transport_company_id' => 'Транспортная компания',
        'shipping_methods'     => 'Метод оплаты',
    ],

    'validation_messages' => [
        'rates_minimum_order_value_min' => ':attribute должна быть не менее 1',
        'rates_maximum_order_value_gt' => ':attribute должна быть не больше чем Минимальная сумма заказа',
    ]
];
