<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'             => 'Название подраздела',
        'parent_section_id' => 'Родительский подраздел',
        'product_group_id'  => 'Тип товара',
        'is_active_tab_parameters'   => 'Активная вкладка "Характеристики" вместо "Описание"',
        'is_bp_disabled'             => 'Товары подраздела не участвуют в расчете уровня бонусной программы',
        'additional_text'            => 'Текст для всех товаров',
        'starting_text_product_name' => 'Название в карточке товара',
        'is_hidden'         => 'Скрытый подраздел',
        'not_for_everyone'  => 'Не для всех',
        'sorting_method'    => 'Способ сортировки товаров',
        'order'             => 'Порядок',
        'seo'               => [
            'desc' => 'Текст для подраздела',
            'starting_phrases' => 'Начальные фразы',
        ],
    ],

    'validation_messages' => [
        'parent_section_id_min' => 'Выберите "Родительский подраздел"',
        'sorting_method_in' => 'Выберите способ сортировки товаров',
    ]
];
