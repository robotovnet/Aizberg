<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'name'   => 'Название проекта',
        'url'    => 'Ссылка на сайт',
        'sort'   => 'Сортировка',
        'banner' => 'Баннер',
    ],

    'validation_messages' => [
        'required'        => 'Заполните поле :attribute',
        'max_string'      => 'Поле :attribute не может быть длиннее :max символов',
        'url'             => 'Поле :attribute должно содержать ссылку, начинающуюся с http:// или https://',
        'integer'         => 'Поле :attribute должно быть целым числом',
        'min_number'      => 'Поле :attribute не может быть меньше :min',
        'max_number'      => 'Поле :attribute не может быть больше :max',
        'banner_required' => 'Загрузите баннер проекта',
        'banner_mimes'    => 'Баннер должен быть файлом формата SVG, PNG, JPG или WEBP',
        'banner_max'      => 'Размер баннера не может превышать :max КБ',
    ],
];
