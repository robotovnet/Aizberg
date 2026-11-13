<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'names'      => 'Price type name',
        'names_lang' => 'Language code',
        'code'       => 'Code',
    ],

    'validation_messages' => [
        'names_required'       => 'Fill in the field :attribute',
        'code_required'        => 'Fill in the field :attribute',
        'code_max'             => 'The code must consist of no more than :max characters',
        'code_unique'          => 'You have already added a price with the code :input',
        'names_lang_required'  => 'Fill in the field :attribute',
        'names_lang_size'      => 'Only two-letter language codes are allowed',
        'names_lang_exists'    => 'Language with code :input does not exist on our system',
        'names_value_required' => 'Fill in the field :attribute',
        'names_value_between'  => 'Price type name must contain from :min to :max characters',
    ]
];
