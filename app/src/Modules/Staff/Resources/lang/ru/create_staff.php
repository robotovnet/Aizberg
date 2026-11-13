<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'first_name'  => 'Имя',
        'last_name'   => 'Фамилия',
        'email'       => 'E-mail адрес',
        'phone'       => 'Телефон',
        'groups'      => 'Список групп',
        'password'    => 'Пароль',
        'groups_item' => 'Группа сотрудников',
        'whatsapp'    => 'WhatsApp',
        'telegram'    => 'Telegram',
        'max'         => 'Max',
    ],

    'validation_messages' => [
        'first_name_required'  => 'Заполните поле :attribute',
        'first_name_between'   => 'Поле :attribute должно содержать от :min до :max символов',
        'last_name_required'   => 'Заполните поле :attribute',
        'last_name_between'    => 'Поле :attribute должно содержать от :min до :max символов',
        'email_required'       => 'Заполните поле :attribute',
        'email_email'          => 'Вы ввели некорректный :attribute',
        'email_max'            => 'Максимально допустимая длина e-mail - :max символов',
        'email_unique'         => ':attribute :input уже используется в системе для другого сотрудника',
        'phone_required'       => 'Заполните поле :attribute',
        'phone_digits_between' => ':attribute должен содержать от :min до :max цифр в номере',
        'phone_unique'         => ':attribute :input уже используется в системе для другого сотрудника',
        'groups_required'      => 'Выберите группу пользователей',
        'password_required'    => 'Заполните поле :attribute',
        'password_between'     => 'Поле :attribute должно содержать от :min до :max символов',
        'groups_item_required' => 'Заполните поле :attribute',
        'groups_item_numeric'  => ':attribute должна содержать числовой идентификатор',
        'groups_item_exists'   => ':attribute не найдена в системе',
        'whatsapp_max'         => 'Максимально допустимая длина :attribute - :max символов',
        'telegram_max'         => 'Максимально допустимая длина :attribute - :max символов',
        'max_max'              => 'Максимально допустимая длина :attribute - :max символов',
    ]
];
