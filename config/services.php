<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'razorpay' => [
        'key' => env('RAZORPAY_KEY_ID'),
        'secret' => env('RAZORPAY_KEY_SECRET'),
    ],

    'way2send' => [
        'enabled'       => env('WAY2SEND_SMS_ENABLED', true),
        'api_url'       => env('WAY2SEND_API_URL', 'https://cpaas.way2send.in/api/sendsms'),
        'api_key'       => env('WAY2SEND_API_KEY', 'f88d8d80d5XX'),
        'user_id'       => env('WAY2SEND_USER_ID', '1101458770000035349'),
        'user_password' => env('WAY2SEND_USER_PASSWORD', ''),
        'sender_id'     => env('WAY2SEND_SENDER_ID', 'TEXCOM'),
        'template_id'   => env('WAY2SEND_TEMPLATE_ID', '1177179144024649608'),
        'pe_id'         => env('WAY2SEND_PE_ID', '1101458770000035349'),
        'verify_ssl'    => env('WAY2SEND_VERIFY_SSL', false),
        'otp_message'   => env('WAY2SEND_OTP_MESSAGE', 'Use OTP :otp for TC-LIMS login. Valid for 5 mins. Keep it secure. -Textiles Committee'),
    ],
];
