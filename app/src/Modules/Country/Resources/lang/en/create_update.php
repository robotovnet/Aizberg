<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'         => 'country name',
        'names_lang'    => 'language code',
        'is_default'    => 'default country',
        'language'      => 'language of the country',
        'code'          => 'country code',
    ],

    'validation_messages' => [
        'names_required'         => 'Fill in the ":attribute" field',
        'is_default_required'    => 'Fill in the ":attribute" field',
        'is_default_in'          => 'Valid field values: "Y" or " N"',
        'language_required'      => 'Select the :attribute',
        'language_size'          => 'Only two-letter language codes are allowed',
        'language_exists'        => 'The language with code :input does not exist in our system',
        'code_required'          => 'Fill in the ":attribute" field',
        'code_size'              => 'Only two-letter country codes are allowed',
        'names_lang_required'    => 'Fill in the ":attribute" field',
        'names_lang_size'        => 'Only two-letter language codes are allowed',
        'names_lang_exists'      => 'The language with the code :input does not exist in our system',
        'names_value_required'   => 'Fill in the ":attribute" field',
        'names_value_between'    => 'The name of the country type must contain from :min to :max characters',
    ]
];
