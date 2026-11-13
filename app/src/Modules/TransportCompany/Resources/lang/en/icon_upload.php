<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'icon' => 'Transport company icon',
    ],

    'validation_messages' => [
        'icon_image'      => 'The uploaded file must be an image (jpg, jpeg, png, bmp, gif, svg, or webp)',
        'icon_dimensions' => 'The image must have min. height and min. width from 16px',
        'icon_min'        => 'The minimum icon size should be from :min kilobytes',
        'icon_max'        => 'The maximum icon size should be up to :max kilobytes',
        'icon'            => ':attribute',
    ]
];
