<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'name'             => 'Название бренда',
        'country_code'     => 'Код страны',
        'official_website' => 'Официальный сайт',
        'desc'             => 'Описание',
        'is_hidden'        => 'Скрыть',
    ],

    'validation_messages' => [
        'country_code_size' => ':attribute обязательно должен быть двухсимвольным',
        ]
];
