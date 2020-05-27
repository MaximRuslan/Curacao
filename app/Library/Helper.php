<?php

namespace App\Library;

use App\Models\Country;
use DateTime;
use DateTimeZone;

class Helper
{
    public static function frontToBackDateTime($date)
    {
        $format = config('site.date_format.php');
        $date = \DateTime::createFromFormat($format, $date);
        return $date->format('Y-m-d H:i:s');
    }

    public static function frontToBackDate($date)
    {
        $format = config('site.date_format.php');
        $date = \DateTime::createFromFormat($format, $date);
        return $date->format('Y-m-d');
    }

    public static function getCountryLogo($country)
    {
        $logo = asset('logo_small.png');
        if ($country != 'user') {
            $country = Country::find($country);
            if ($country != '') {
                if ($country->logo != '') {
                    $logo = asset('uploads/' . $country->logo);
                }
            }
        } else {
            $logo = asset('resources/img/client/logo-high.png');
        }
        return $logo;
    }

    public static function date_time_to_current_timezone($date_time, $timezone = null)
    {
        $utc = $date_time;
        $dt = new \DateTime($utc);
        if ($timezone != null) {
            $tz = new \DateTimeZone($timezone);
            $dt->setTimezone($tz);
        } else if (session()->has('timezone') || !auth()->user()->hasRole('super admin')) {
            $tz = new \DateTimeZone(session('timezone'));
            $dt->setTimezone($tz);
        } else {
            $tz = new \DateTimeZone(config('site.super_admin_timezone'));
            $dt->setTimezone($tz);
        }
        return $dt->format('d M Y h:i:s A');
    }

    public static function time_to_current_timezone($date_time, $timezone = null, $format = null)
    {
        if ($format == null) {
            $format = 'h:i:s A';
        }
        $utc = $date_time;
        $dt = new \DateTime($utc);
        if ($timezone != null) {
            $tz = new \DateTimeZone($timezone);
            $dt->setTimezone($tz);
        } else if (session()->has('timezone') || !auth()->user()->hasRole('super admin')) {
            $tz = new \DateTimeZone(session('timezone'));
            $dt->setTimezone($tz);
        } else {
            $tz = new \DateTimeZone(config('site.super_admin_timezone'));
            $dt->setTimezone($tz);
        }
        return $dt->format($format);
    }

    public static function date_time_to_current_timezone_without_seconds($date_time, $timezone = null)
    {
        $utc = $date_time;
        $dt = new \DateTime($utc);
        if ($timezone != null) {
            $tz = new \DateTimeZone($timezone);
            $dt->setTimezone($tz);
        } else if (session()->has('timezone') || !auth()->user()->hasRole('super admin')) {
            $tz = new \DateTimeZone(session('timezone'));
            $dt->setTimezone($tz);
        } else {
            $tz = new \DateTimeZone(config('site.super_admin_timezone'));
            $dt->setTimezone($tz);
        }
        return $dt->format('d M Y, h:i A');
    }

    public static function date_to_current_timezone($date, $timezone = null, $format = null)
    {
        if ($format == null) {
            $format = 'd M Y';
        }
        $utc = $date;
        $dt = new \DateTime($utc);
        if ($timezone != null) {
            $tz = new \DateTimeZone($timezone);
            $dt->setTimezone($tz);
        } else if (session()->has('timezone') || (auth()->check() && !auth()->user()->hasRole('super admin'))) {
            $tz = new \DateTimeZone(session('timezone'));
            $dt->setTimezone($tz);
        } else {
            $tz = new \DateTimeZone(config('site.super_admin_timezone'));
            $dt->setTimezone($tz);
        }
        return $dt->format($format);
    }

    public static function database_date_to_current_timezone($date, $timezone = null)
    {
        $utc = $date;
        $dt = new \DateTime($utc);
        if ($timezone != null) {
            $tz = new \DateTimeZone($timezone);
            $dt->setTimezone($tz);
        } else if (session()->has('timezone') || !auth()->user()->hasRole('super admin')) {
            $tz = new \DateTimeZone(session('timezone'));
            $dt->setTimezone($tz);
        } else {
            $tz = new \DateTimeZone(config('site.super_admin_timezone'));
            $dt->setTimezone($tz);
        }
        return $dt->format('Y-m-d');
    }

    public static function database_date_time_to_current_timezone($date, $timezone = null)
    {
        $utc = $date;
        $dt = new \DateTime($utc);
        if ($timezone != null) {
            $tz = new \DateTimeZone($timezone);
            $dt->setTimezone($tz);
        } else if (session()->has('timezone') || !auth()->user()->hasRole('super admin')) {
            $tz = new \DateTimeZone(session('timezone'));
            $dt->setTimezone($tz);
        } else {
            $tz = new \DateTimeZone(config('site.super_admin_timezone'));
            $dt->setTimezone($tz);
        }
        return $dt->format('Y-m-d H:i:s');
    }

    public static function date_to_sheet_timezone($date, $timezone = null)
    {
        $utc = $date;
        $dt = new \DateTime($utc);
        if ($timezone != null) {
            $tz = new \DateTimeZone($timezone);
            $dt->setTimezone($tz);
        } else if (session()->has('timezone') || !auth()->user()->hasRole('super admin')) {
            $tz = new \DateTimeZone(session('timezone'));
            $dt->setTimezone($tz);
        } else {
            $tz = new \DateTimeZone(config('site.super_admin_timezone'));
            $dt->setTimezone($tz);
        }
        return $dt->format('d/m/Y');
    }

    public static function currentTimezoneToUtcDateTime($dt, $timezone = null)
    {
        if ($timezone == null) {
            if (session()->has('timezone') || !auth()->user()->hasRole('super admin')) {
                $timezone = session('timezone');
            } else {
                $timezone = config('site.super_admin_timezone');
            }
        }
        $given = new \DateTime($dt, new \DateTimeZone($timezone));
        $given->setTimezone(new \DateTimeZone("UTC"));
        return $given->format("Y-m-d H:i:s");
    }

    public static function datebaseToFrontDate($date)
    {
        return date('d M Y', strtotime($date));
    }

    public static function databaseToFrontEditDate($date, $format = 'd/m/Y')
    {
        return date($format, strtotime($date));
    }

    public static function datebaseToSheetDate($date, $timezone = null)
    {
        $utc = $date;
        $dt = new \DateTime($utc);
        if ($timezone != null) {
            $tz = new \DateTimeZone($timezone);
            $dt->setTimezone($tz);
        } else {
            $tz = new \DateTimeZone(config('site.super_admin_timezone'));
            $dt->setTimezone($tz);
        }
        return $dt->format('d/m/Y');
    }

    public static function isValidTimezone($timezone)
    {
        return in_array($timezone, timezone_identifiers_list());
    }

    public static function base64ToJpeg($base64String, $name)
    {
        $date = new \DateTime();
        $path = 'uploads/';
        $filename = time() . '_' . $name . '.jpg';
        $outputFile = public_path() . '/' . $path . '/' . $filename;
        if (!file_exists(public_path() . '/' . $path)) {
            mkdir(public_path() . '/' . $path);
        }
        $data_uri = $base64String;
        $encoded_image = explode(",", $data_uri)[1];
        $decoded_image = base64_decode($encoded_image);
        file_put_contents(public_path() . '/' . $path . '/' . $filename, $decoded_image);
        return $filename;
    }

    public static function returnUrl($inputs)
    {
        $str = '';
        if (isset($inputs['route']) && $inputs['route'] != '' && isset($inputs['route_param'])) {
            $str .= route($inputs['route'], $inputs['route_param']);
        } else if (isset($inputs['route']) && $inputs['route'] != '') {
            $str .= route($inputs['route']);
        } else {
            $str .= '#nogo';
        }
        if (isset($inputs['query_string']))
            $str .= '?' . $inputs['query_string'];
        return $str;
    }

    public static function siteFavicon()
    {
        return asset('resources/img/client/hyla-favicon.png');
    }

    public static function decimalRound2($value)
    {
        return round($value, 2);
    }

    public static function numberShowing($value)
    {
        return number_format(intval($value), 0, '.', ',');
    }

    public static function getCountryId()
    {
        $country = null;

        if (request('country') && auth()->user()->hasRole('super admin')) {
            $country = request('country');
        } else {
            if (!auth()->user()->hasRole('super admin')) {
                $country = auth()->user()->country;
            }

            if (auth()->user()->hasRole('super admin') && session()->has('country')) {
                $country = session('country');
            }
        }
        return $country;
    }

    public static function decimalShowing($value, $country_id)
    {
        $upto = config('site.default_upto');
        $country = null;
        if ($country_id == null) {
            $upto = config('site.super_admin_upto');
        } else {
            $country = Country::find($country_id);
        }
        if ($country != null && $country->decimal == 1) {
            $upto = config('site.country_upto');
        }
        return number_format(round($value, $upto), config('site.decimal_upto'), '.', ',');
    }

    public static function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {

        // Declare an empty array
        $array = array();

        // Variable that store the date interval
        // of period 1 day
        $interval = new \DateInterval('P1D');

        $realEnd = new \DateTime($end);
        $realEnd->add($interval);

        $period = new \DatePeriod(new \DateTime($start), $interval, $realEnd);

        // Use loop to store date into array
        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        // Return the array elements
        return $array;
    }

    public static function authMerchant()
    {
        return auth()->guard('merchant')->check();
    }

    public static function authMerchantUser()
    {
        return auth()->guard('merchant')->user();
    }

    public static function getMerchantId()
    {
        $user = self::authMerchantUser();
        if ($user->type == 1) {
            return $user->id;
        } else {
            return $user->merchant_id;
        }
    }

    public static function timezoneToOffset($timezone)
    {
        $dateTimeZoneJapan = new DateTimeZone($timezone);
        $dateTimeTaipei = new DateTime("now", new DateTimeZone($timezone));
        $offset = $dateTimeZoneJapan->getOffset($dateTimeTaipei);
        $minus = false;
        if ($offset < 0) {
            $minus = true;
        }
        $str = '+';
        if ($minus) {
            $str = '-';
        }
        $str .= gmdate('H:i', abs($offset));
        return $str;
    }
}
