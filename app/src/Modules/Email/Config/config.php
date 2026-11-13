<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | MODULE
    |--------------------------------------------------------------------------
    */

    // Module Name
    'name' => 'Email',

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    */

    // Sendbox
    'replyToEmail'        => env('MAIL_SENDBOX_REPLY_TO'),
    'apiSendMailEndpoint' => 'https://mailer-api.i.bizml.ru/smtp/emails',
    'getTokenEndpoint'    => 'https://mailer-api.i.bizml.ru/oauth/access_token',
    'fromEmail'           => env('MAIL_SENDBOX_FROM'),
    'apiClientId'         => env('MAIL_SENDBOX_API_CLIENT_ID'),
    'apiClientSecret'     => env('MAIL_SENDBOX_API_CLIENT_SECRET'),

    // Mailgun
    'mailgun_domain' => env('MAILGUN_DOMAIN'),
    'mailgun_secret' => env('MAILGUN_SECRET'),
];
