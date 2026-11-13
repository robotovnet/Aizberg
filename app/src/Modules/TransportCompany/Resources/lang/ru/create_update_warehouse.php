<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'name'                    => 'Название склада',
        'label_1c'                => 'Метка для 1С',
        'code'                    => 'Code',
        'priority'                => 'Приоритет склада',
        'is_active'               => 'Активный склад?',
        'transport_companies'     => 'Список ТК',
    ],

    'validation_messages' => [
        'transport_companies_required' => 'Хотя бы одна ТК должна быть выбрана',
    ]
];
