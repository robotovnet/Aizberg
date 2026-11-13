<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Form Language Lines
    |--------------------------------------------------------------------------
    */

    'validation_attributes' => [
        'userpic' => 'User picture',
    ],

    'validation_messages' => [
        'userpic_required'    => 'You must pass the image :attribute',
        'userpic_image'       => 'The uploaded file must be an image (jpg, jpeg, png, bmp, gif, svg, or webp)',
        'userpic_dimensions'  => 'The image must have min. height and min. width from 100px',
        'userpic_min'         => 'The minimum size of the user picture must be from :min kilobytes',
        'userpic_max'         => 'The maximum size of the user picture should be up to :max kilobytes',
    ]
];
