<?php

namespace App\Library;


class EmailHelper
{
    public static function emailConfigChanges($type = null)
    {
        if ($type == 'user') {
            config([
//                'mail.host'         => 'mail.hylawallet.com',
'mail.host'                  => 'usm83.siteground.biz',
'mail.username'              => 'noreply@hylawallet.com',
'mail.from.address'          => 'noreply@hylawallet.com',
'mail.from.name'             => 'Hyla Wallet',
'mail.port'                  => 587,
'mail.encryption'            => env('MAIL_ENCRYPTION'),
'site.footer_mail_email_web' => 'info@hylawallet.com'
            ]);
        } else if ($type == 'st_marten') {
            config([
                'mail.host'                  => 'usm83.siteground.biz',
                'mail.username'              => 'noreply@caribbeancash.net',
                'mail.from.address'          => 'noreply@caribbeancash.net',
                'mail.from.name'             => 'Caribbeancash',
                'mail.port'                  => 587,
                'mail.encryption'            => env('MAIL_ENCRYPTION'),
                'site.footer_mail_email_web' => 'info@caribbeancash.cw'
            ]);
        } else if ($type == 'curacao') {
            config([
                'mail.host'                  => 'usm83.siteground.biz',
                'mail.username'              => 'noreply@caribbeancash.net',
                'mail.from.address'          => 'noreply@caribbeancash.net',
                'mail.from.name'             => 'Caribbeancash',
                'mail.port'                  => 587,
                'mail.encryption'            => env('MAIL_ENCRYPTION'),
                'site.footer_mail_email_web' => 'info@caribbeancash.cw'
            ]);
        } else if ($type == 'costa_rica') {
            config([
                'mail.host'                  => 'usm83.siteground.biz',
                'mail.username'              => 'noreply@puravidacash.com',
                'mail.from.address'          => 'noreply@puravidacash.com',
                'mail.from.name'             => 'Puravida Cash',
                'mail.port'                  => 587,
                'mail.encryption'            => env('MAIL_ENCRYPTION'),
                'site.footer_mail_email_web' => 'info@puravidacash.com'
            ]);
        } else {
            config([
                'mail.host'                  => env('MAIL_HOST'),
                'mail.username'              => env('MAIL_USERNAME'),
                'mail.from.address'          => env('MAIL_USERNAME'),
                'mail.from.name'             => 'Caribbeancash',
                'mail.password'              => env('MAIL_PASSWORD'),
                'mail.port'                  => env('MAIL_PORT'),
                'mail.encryption'            => env('MAIL_ENCRYPTION'),
                'site.footer_mail_email_web' => 'info@hylawallet.com'
            ]);
        }
    }
}