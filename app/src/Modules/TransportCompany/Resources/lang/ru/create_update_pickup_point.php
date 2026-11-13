<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'name'            => 'Название офиса',
        'country_id'      => 'Страна',
        'city_id'         => 'Город',
        'adding_method'   => 'Способ добавления',
        'address'         => 'Адрес',
        'address_comment' => 'Комментарий к адресу',
        'payment_by_card' => 'Оплата картой',
        'payment_by_cash' => 'Оплата наличными',
        'postal_code'     => 'Почтовый индекс',
        'phone'           => 'Телефон',
        'lat'             => 'Широта',
        'lng'             => 'Долгота',
        'metro_station'   => 'Ближайшее метро',
        'work_time'       => 'График работы',
        'break_work_time' => 'Перерыв в работе',
        'transport_companies' => 'Транспортные компании',
        'warehouses'      => 'Отгрузка со склада',
    ],

    'validation_messages' => [
        'country_id_min'  => 'Выберите страну',
        'city_id_min'     => 'Выберите город',
    ],
];
