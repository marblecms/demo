<?php

return [
    /*
     | Sender name used for all newsletter emails.
     */
    'from_name' => env('NEWSLETTER_FROM_NAME', env('APP_NAME', 'Newsletter')),

    /*
     | Sender email address.
     */
    'from_email' => env('NEWSLETTER_FROM_EMAIL', env('MAIL_FROM_ADDRESS', 'newsletter@example.com')),

    /*
     | Enable double opt-in confirmation. When true, a confirmation email is sent
     | and the subscriber must click the link before they are marked confirmed.
     */
    'double_opt_in' => env('NEWSLETTER_DOUBLE_OPT_IN', true),

    /*
     | Subject line for the confirmation email.
     */
    'confirmation_subject' => 'Please confirm your subscription',

    /*
     | View to use for the confirmation email body.
     | Override via publishing newsletter-views.
     */
    'confirmation_view' => 'newsletter::emails.confirmation',
];
