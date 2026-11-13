<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'              => 'Название',
        'overview_texts'     => 'Описание',
        'catalog_section_id' => 'Категория',
        'prices'             => 'Цена',
        'vendor_code'        => 'Артикул',
        'brand_id'           => 'Бренд',
        'product_group_id'   => 'Тип товара',
        'is_hidden'          => 'Скрыть продукт',
        'not_for_everyone'   => 'Не для всех',
        'stock_status'       => 'Наличие продукта:',
        'ad_yandex_market'               => 'Любое поле, относящее к настройкам рекламы на Яндекс.Маркет',
        'ad_yandex_market_name'          => 'Название для Яндекс.Маркета',
        'ad_yandex_market_overview_text' => 'Описание товара для Яндекс.Маркета (YML)',
    ],

    'validation_messages' => [
        'brand_id_min'            => 'Выберите бренд',
        'product_group_id_min'    => 'Выберите тип товара',
        'catalog_section_id_min'  => 'Выберите подраздел',
    ]
];
