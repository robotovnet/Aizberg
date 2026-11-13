<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'first_name'  => 'First name',
        'last_name'   => 'Last name',
        'email'       => 'E-mail',
        'phone'       => 'Phone',
        'groups'      => 'List of groups',
        'password'    => 'Password',
        'groups_item' => 'Staff Group',
        'whatsapp'    => 'WhatsApp',
        'telegram'    => 'Telegram',
        'max'         => 'Max',
    ],

    'validation_messages' => [
        'first_name_required'  => 'Fill in the field :attribute',
        'first_name_between'   => 'The :attribute field must contain from :min to :max characters',
        'last_name_required'   => 'Fill in the field :attribute',
        'last_name_between'    => 'The :attribute field must contain from :min to :max characters',
        'email_required'       => 'Fill in the field :attribute',
        'email_email'          => 'You entered an incorrect :attribute',
        'email_max'            => 'The maximum allowed length of an e-mail is :max characters',
        'email_unique'         => ':attribute :input is already used in the system for another staff',
        'phone_required'       => 'Fill in the field :attribute',
        'phone_digits_between' => ':attribute must contain from :min to :max digits in the number',
        'phone_unique'         => ':attribute :input is already used in the system for another staff',
        'groups_required'      => 'You need to pass :attribute',
        'password_required'    => 'Fill in the field :attribute',
        'password_between'     => 'The :attribute field must contain from :min to :max characters',
        'groups_item_required' => 'Fill in the field :attribute',
        'groups_item_numeric'  => ':attribute must contain a numeric identifier',
        'groups_item_exists'   => ':attribute not found in the system',
        'whatsapp_max'         => 'Maximum allowed length of :attribute is :max characters',
        'telegram_max'         => 'Maximum allowed length of :attribute is :max characters',
        'max_max'              => 'Maximum allowed length of :attribute is :max characters',
    ]
];
