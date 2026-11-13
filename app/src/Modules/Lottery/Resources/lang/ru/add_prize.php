<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'product_vendor_code'     => 'Артикул продукта',
    ],

    'validation_messages' => [
        'product_vendor_code_required' => 'Заполните поле :attribute',
        'product_vendor_code_exists' => 'Продукт с таким артикулом не найден',
    ]
];
