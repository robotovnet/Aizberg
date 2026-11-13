<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'name'                => 'название группы',
        'permissions'         => 'права группы',
        'permissions_policy'  => 'разрешение',
        'start_policy'        => 'Страница по умолчанию',
    ],

    'validation_messages' => [
        'name_required'               => 'Заполните поле :attribute',
        'name_max'                    => ':attribute не должно быть длинее :max символов',
        'name_unique'                 => 'Уже есть группа с названием :input',
        'permissions_required'        => 'Отсутствует список разрешений для группы',
        'permissions_policy_required' => 'Требуется указать хотя бы одно :attribute для группы',
        'permissions_policy_max'      => ':attribute не должно быть длинее :max символов',
        'permissions_policy_distinct' => ':attribute повторяется',
    ]
];
