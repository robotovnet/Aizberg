<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'country_id'          => 'Идентификатор страны',
        'city_id'             => 'Идентификатор города',
        'adding_method'       => 'Способ добавления',
        'warehouses'          => 'Склад',
        'transport_companies' => 'Транспортная компания',
    ],

    'validation_messages' => [
        'country_id_required'          => 'Заполните поле :attribute',
        'city_id_required'             => 'Заполните поле :attribute',
        'adding_method_required'       => 'Заполните поле :attribute',
        'warehouses_required'          => 'Заполните поле :attribute',
        'transport_companies_required' => 'Заполните поле :attribute',
        'country_id_numeric'           => 'Поле :attribute должно содержать число',
        'country_id_min'               => 'Выберите страну',
        'country_id_exists'            => 'Страна с ID = :input не существует в системе',
        'city_id_numeric'              => 'Поле :attribute должно содержать число',
        'city_id_min'                  => 'Выберите город',
        'city_id_exists'               => 'Город с ID = :input не существует в системе',
        'adding_method_in'             => 'Поле :attribute поддерживает следующие значения: preset, manual',
        'warehouses_array'             => 'Поле :attribute должно содержать массив идентификаторов складов',
        'transport_companies_array'    => 'Поле :attribute должно содержать массив идентификаторов ТК',
        'warehouses_numeric'           => 'Идентификатор склада должен быть числом',
        'warehouses_min'               => 'Идентификатор склада должен быть числом равным или большим единицы',
        'warehouses_exists'            => 'Склад с ID = :input не существует в системе',
        'transport_companies_numeric'  => 'Идентификатор ТК должен быть числом',
        'transport_companies_min'      => 'Идентификатор ТК должен быть числом равным или большим единицы',
        'transport_companies_exists'   => 'ТК с ID = :input не существует в системе',
    ]
];
