<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'         => 'name of the currency',
        'names_lang'    => 'language code',
        'country_code'  => 'country',
        'is_default'    => 'default currency',
        'code'          => 'currency code',
        'symbol'        => 'currency sign',
        'extra_charge'  => 'extra charge',
    ],

    'validation_messages' => [
        'names_required'        => 'Fill in the ":attribute" field',
        'is_default_required'   => 'Fill in the ":attribute" field',
        'is_default_in'         => 'Valid field values: "Y" or " N"',
        'names_lang_required'   => 'Fill in the ":attribute" field',
        'names_lang_size'       => 'Only two-letter language codes are allowed',
        'names_lang_exists'     => 'The language with the code :input does not exist in our system',
        'names_value_required'  => 'Fill in the ":attribute" field',
        'names_value_between'   => 'The currency name must contain from :min to :max characters',
        'code_required'         => 'Fill in the ":attribute" field',
        'code_size'             => ':attribute must be three-character',
        'symbol_required'       => 'Fill in the ":attribute" field',
        'symbol_between'        => ':attribute must contain no more than :max characters',
        'extra_charge_required' => 'Fill in the ":attribute" field',
        'extra_charge_numeric'  => 'The :attribute field must contain a number',
        'extra_charge_between'  => 'The field value must be in the range from :min to :max',
        'country_code_required' => 'Select a country',
        'country_code_exists'   => 'The country with the code :input was not found in the system',
        'country_code_unique'   => 'You have previously added a currency for the country with the code :input',
    ]
];

