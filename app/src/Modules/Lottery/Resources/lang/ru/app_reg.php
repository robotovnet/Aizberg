<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'player_name'     => 'Фамилия Имя',
        'player_phone'    => 'Телефон',
        'player_email'    => 'Электронная почта',
        'ticket_code'     => 'Номер билета',
        'screenshot'      => 'Скриншот',
    ],

    'validation_messages' => [
        'player_name_required' => 'Заполните поле :attribute',
        'player_name_min' => 'Минимальная длина поля :attribute 3 символа',
        'player_name_max' => 'Максимальная длина поля :attribute 60 символов',
        'player_phone_required' => 'Заполните поле :attribute',
        'player_phone_regex' => 'Некорректный формат телефона',
        'player_email_required' => 'Заполните поле :attribute',
        'player_email_email' => 'Некорректный формат электронной почты',
        'ticket_code_required' => 'Заполните поле :attribute',
        'ticket_code_exists' => 'Билет с таким номером не найден в системе',
        'screenshot_required' => 'Заполните поле :attribute',
        'screenshot_image' => 'Файл должен быть изображением',
        'screenshot_dimensions' => 'Минимальный допустимый размер изображения 200x200px',
        'screenshot_min' => 'Минимальный допустимый размер изображения 1 кб',
        'screenshot_max' => 'Максимальный допустимый размер изображения 7 Мб',
        'screenshot_mimes' => 'Можно загружать скриншоты только в форматах: jpeg, jpg, png, gif',
    ]
];
