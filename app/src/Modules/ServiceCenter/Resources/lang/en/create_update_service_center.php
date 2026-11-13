<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'name'            => 'Название сервисного центра',
        'country_id'      => 'Страна',
        'city_id'         => 'Город',
        'adding_method'   => 'Способ добавления',
        'address'         => 'Адрес',
        'address_comment' => 'Комментарий к адресу',
        'postal_code'     => 'Почтовый индекс',
        'site'            => 'Веб-сайт',
        'phone'           => 'Телефон',
        'lat'             => 'Широта',
        'lng'             => 'Долгота',
        'metro_station'   => 'Ближайшее метро',
        'work_time'       => 'График работы',
        'break_work_time' => 'Перерыв в работе',
    ],

    'validation_messages' => [
        'country_id_min'  => 'Выберите страну',
        'city_id_min'     => 'Выберите город',
    ],
];
