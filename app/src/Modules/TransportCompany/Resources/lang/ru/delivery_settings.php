<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'days_transfer'                  => 'Кол-во дней требуемых для доставки товара в город доставки',
        'transfer_time_tomorrow'         => 'Время, когда передача товара в ТК переносится на завтра',
        'transfer_time_after_tomorrow'   => 'Время, когда передача товара в ТК переносится на послезавтра',
        'transport_company_working_days' => 'Дни передачи товара в ТК',
        'transport_company_weekends'     => 'Выходные ТК',
        'delivery_type'                  => 'Тип доставки',
        'delivery_point_id'              => 'Идентификатор объекта доставки',
        'transport_company_id'           => 'Транспортная компания',
    ],

    'validation_messages' => [
        'days_transfer_required'                    => 'Заполните поле :attribute',
        'transfer_time_tomorrow_required'           => 'Заполните поле :attribute',
        'transfer_time_after_tomorrow_required'     => 'Заполните поле :attribute',
        'transport_company_working_days_required'   => 'Заполните поле :attribute',
        'transport_company_weekends_required'       => 'Заполните поле :attribute',
        'delivery_type_required'                    => 'Заполните поле :attribute',
        'transport_company_id_required'             => 'Заполните поле :attribute',
        'days_transfer_numeric'                     => 'Поле :attribute должно содержать число',
        'days_transfer_min'                         => 'Поле :attribute должно содержать число равное или большее нуля',
        'transfer_time_tomorrow_date_format'        => 'Время в поле :attribute необходимо вводить в формате: ЧЧ:ММ',
        'transfer_time_after_tomorrow_date_format'  => 'Время в поле :attribute необходимо вводить в формате: ЧЧ:ММ',
        'delivery_type_in'                          => 'Поддерживаемые типы доставки: postamat, courier_city или pickup_point',
        'delivery_point_id_numeric'                 => 'Идентификатор объекта доставки должен быть числом',
        'delivery_point_id_min'                     => 'Идентификатор объекта доставки должен быть числом равным или большим единицы',
        'transport_company_id_exists'               => 'ТК с ID = :input не существует в системе',
        'transport_company_id_numeric'              => 'Идентификатор ТК должен быть числом',
        'transport_company_id_min'                  => 'Идентификатор ТК должен быть числом равным или большим единицы',
        'days_names'                                => 'Принимаются следующие значения названий дней недели: Mn, Tu, Wd, Th, Fr, Sa, Sn',
    ]
];
