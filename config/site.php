<?php
return [
    'date_format'              => [
        'php' => 'd/m/Y',
        'js'  => 'dd/mm/yyyy'
    ],
    'employment_type'          => [
        '3' => 'Contract',
        '1' => 'Full Time',
        '2' => 'Part Time',
    ],
    'status'                   => [
        '1' => 'Requested',
        '2' => 'Completed',
        '3' => 'Cancelled',
    ],
    'language'                 => [
        'eng' => 'ENG',
        'esp' => 'ESP',
        'pap' => 'PAP',
    ],
    'lang'                     => [ //this is use for display dynamic data from db at client side
                                    'eng' => 'eng',
                                    'esp' => 'esp',
                                    'pap' => 'pap',
    ],
    'payment_frequency'        => [
        '2' => 'Bi-Weekly',
        '3' => 'Monthly',
        '1' => 'Weekly',
    ],
    'payment_types'            => [
        '5' => 'Bank Transfer',
        '6' => 'Cheque',
        '4' => 'Maestro',
        '2' => 'Petty cash',
        '1' => 'Vault',
        '3' => 'Vault door',
    ],
    'cash_back_payment_types'  => [
        '1' => 'Vault',
        '2' => 'Petty cash',
    ],
    'credit_payment_types'     => [
        '2' => 'Bank Transfer',
        '1' => 'Cash Transfer',
    ],
    'blacklistedUser'          => 4,
    'inactivity_logout'        => 45,
    'super_admin_timezone'     => 'America/Curacao',
    'media_type'               => [
        '1' => 'User Other Documents'
    ],
    'debt_collection_fee_type' => [
//        '3' => 'Debt Collection Fee %',
'2' => 'Flat fee p/wk',
'1' => 'Percentage p/wk',
    ],

    'late_audit_report_time' => '-24 hours',
    // 'late_audit_report_time'   => '-2 min',
    // 'late_cash_payout_time'    => '-2 min'
    'late_cash_payout_time'  => '-25 min',

    'firebase_server_key'         => env('FIREBASE_SERVER_KEY'),
    'page_title'                  => '&bull; ' . env('APP_NAME'),
    'logo'                        => 'resources/img/client/logo.png',
    'admin_logo'                  => 'resources/img/admin/logo.png',
    'cron_auto_mode'              => env('CRON_AUTO_MODE'),
    "civil_statues"               => [
        '2' => 'Married',
        '3' => 'Divorced',
        '1' => 'Single',
        '4' => 'Partnership'
    ],
    // 'bcc_users'                   => 'hylawallet@gmail.com',
    'bcc_users'                   => [],
    'footer_mail_email_web'       => '',
    'web_verified_delete_hours'   => '48',
    'raffle'                      => env('RAFFLE', false),
    'raffle_participation_period' => 89,
    'super_admin_upto'            => 2,
    'default_upto'                => 0,
    'country_upto'                => 2,
    'decimal_upto'                => 2,
    'sender_number'               => env('SENDER_MOBILE_NO'),
    'auth_id'                     => env('TWILLIO_AUTH_ID'),
    'auth_token'                  => env('TWILLIO_AUTH_TOKEN'),
    'sms_send'                    => env('MESSAGE_ENABLE', false),
    'loan_statuses'               => [
        1 => 'With Loans',
        2 => 'Without Loans'
    ],
    'merchant_types'              => [
        1 => 'Merchant',
        2 => 'Sub User'
    ],
    'reconciliation_status'       => [
        1 => 'Start',
        2 => 'Finished'
    ]
];
