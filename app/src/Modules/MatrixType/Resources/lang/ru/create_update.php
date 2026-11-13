<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'name' => 'Название',
        'margin_up_to_BP' => 'Маржа до BP',
        'EXW_price' => 'Цена EXW',
        'weight_in_kg' => 'Вес в кг',
        'MRP_markup' => 'Наценка MRP',
        'RRP_markup' => 'Наценка RRP',
        'margin_up_to_P2' => 'Маржа до P2',
        'margin_up_to_P3' => 'Маржа до P3',
        'margin_up_to_P4' => 'Маржа до P4',
        'mark_up_contract' => 'Наценка по договору',
    ],

    'validation_messages' => [
        'name_required' => 'Заполните поле :attribute',
        'margin_up_to_BP_required' => 'Заполните поле :attribute',
        'EXW_price_required' => 'Заполните поле :attribute',
        'weight_in_kg_required' => 'Заполните поле :attribute',
        'MRP_markup_required' => 'Заполните поле :attribute',
        'RRP_markup_required' => 'Заполните поле :attribute',
        'margin_up_to_P2_required' => 'Заполните поле :attribute',
        'margin_up_to_P3_required' => 'Заполните поле :attribute',
        'margin_up_to_P4_required' => 'Заполните поле :attribute',
        'mark_up_contract_required' => 'Заполните поле :attribute',
    ]
];
