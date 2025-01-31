<?php

namespace App\Helpers;

use DateTime;

class SystemHelper
{
    public static function timeElapsedString($dateTime)    { 
        $timestamp = strtotime($dateTime);
        if ($timestamp === false) {
            return 'Invalid date';
        }
    
        $currentTimestamp = time();
        $diffSeconds = $currentTimestamp - $timestamp;
    
        if ($diffSeconds > 86400) {
            return date('M j, Y \a\t h:i:s A', $timestamp);
        }
    
        $intervals = [
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        ];
    
        if ($diffSeconds < 60) {
            return 'Just Now';
        }
    
        foreach ($intervals as $seconds => $label) {
            $count = floor($diffSeconds / $seconds);
            if ($count > 0) {
                return sprintf('%d %s ago', $count, $label . ($count > 1 ? 's' : ''));
            }
        }
    
        return date('M j, Y \a\t h:i:s A', $timestamp);
    }
    
    public static function yearMonthElapsedComparisonString($startDateTime, $endDateTime)    {
        $startDate = DateTime::createFromFormat('d F Y', '01 ' . $startDateTime);
        $endDate = DateTime::createFromFormat('d F Y', '01 ' . $endDateTime);

        if ($startDate && $endDate) {
            $interval = $startDate->diff($endDate);
            $years = $interval->y;
            $months = $interval->m;

            $elapsedTime = [];
            if ($years > 0) {
                $elapsedTime[] = $years . ' ' . ($years === 1 ? 'year' : 'years');
            }
            if ($months > 0) {
                $elapsedTime[] = $months . ' ' . ($months === 1 ? 'month' : 'months');
            }

            return $elapsedTime ? implode(' and ', $elapsedTime) : 'Just Now';
        }
        return 'Error parsing dates';
    }
    
    public static function formatDate($format, $date, $modify = null)    {
        $dateTime = new DateTime($date);
        if ($modify) {
            $dateTime->modify($modify);
        }
        return $dateTime->format($format);
    }

    public static function formatDuration($lockDuration) {
        $durationParts = [];
    
        $timeUnits = [
            ['year', 60 * 60 * 24 * 365],
            ['month', 60 * 60 * 24 * 30],
            ['day', 60 * 60 * 24],
            ['hour', 60 * 60], 
            ['minute', 60],
            ['second', 1]
        ];
    
        foreach ($timeUnits as list($unit, $seconds)) {
            $value = floor($lockDuration / $seconds);
            
            $lockDuration %= $seconds;
    
            if ($value > 0) {
                $durationParts[] = number_format($value) . ' ' . $unit . ($value > 1 ? 's' : '');
            }
        }
    
        if (empty($durationParts)) {
            return ['less than a second'];
        }
    
        return $durationParts;
    }
    
    
    public static function getDefaultReturnValue($type, $systemDate, $systemTime)    {
        switch ($type) {
            case 'default':
                return $systemDate;
            case 'empty':
            case 'attendance empty':
            case 'summary':
                return null;
            case 'na':
                return 'N/A';
            case 'complete':
            case 'encoded':
            case 'date time':
                return 'N/A';
            case 'default time':
                return $systemTime;
            default:
                return null;
        }
    }
    
    private static function needsTime($type)    {
        return in_array($type, ['complete', 'encoded', 'date time']);
    }
    
    public static function getDefaultImage($type)    {
        $defaultImages = [
            'profile' => DEFAULT_AVATAR_IMAGE,
            'login background' => DEFAULT_BG_IMAGE,
            'login logo' => DEFAULT_LOGIN_LOGO_IMAGE,
            'menu logo' => DEFAULT_MENU_LOGO_IMAGE,
            'module icon' => DEFAULT_MODULE_ICON_IMAGE,
            'favicon' => DEFAULT_FAVICON_IMAGE,
            'company logo' => DEFAULT_COMPANY_LOGO,
            'id placeholder front' => DEFAULT_ID_PLACEHOLDER_FRONT,
            'app module logo' => DEFAULT_APP_MODULE_LOGO,
            'upload placeholder' => DEFAULT_UPLOAD_PLACEHOLDER,
        ];

        return $defaultImages[$type] ?? DEFAULT_PLACEHOLDER_IMAGE;
    }
    
    public static function checkImage($image, $type)    {
        $image = $image ?? '';
        $imagePath = str_replace('./apps/', '../../../../apps/', $image);

        return (empty($image) || !file_exists($imagePath) && !file_exists($image))
            ? self::getDefaultImage($type)
            : $image;
    }
    
    public static function getFileExtensionIcon($type)    {
        $defaultImages = [
            'ai' => './assets/images/file-icon/img-file-ai.svg',
            'doc' => './assets/images/file-icon/img-file-doc.svg',
            'docx' => './assets/images/file-icon/img-file-doc.svg',
            'jpeg' => './assets/images/file-icon/img-file-img.svg',
            'jpg' => './assets/images/file-icon/img-file-img.svg',
            'png' => './assets/images/file-icon/img-file-img.svg',
            'gif' => './assets/images/file-icon/img-file-img.svg',
            'pdf' => './assets/images/file-icon/img-file-pdf.svg',
            'ppt' => './assets/images/file-icon/img-file-ppt.svg',
            'pptx' => './assets/images/file-icon/img-file-ppt.svg',
            'rar' => './assets/images/file-icon/img-file-rar.svg',
            'txt' => './assets/images/file-icon/img-file-txt.svg',
            'xls' => './assets/images/file-icon/img-file-xls.svg',
            'xlsx' => './assets/images/file-icon/img-file-xls.svg',
        ];

        return $defaultImages[$type] ?? './assets/images/file-icon/img-file-img.svg';
    }

    public static function getFormatBytes($bytes, $precision = 2)    {
        $units = ['B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'];

        $bytes = max($bytes, 0);
        $pow = floor(log($bytes ?: 1, 1024));
        return round($bytes / (1 << (10 * $pow)), $precision) . ' ' . $units[$pow];
    }
    
    public static function generateMonthOptions()    {
        $months = [
            'January', 'February', 'March', 'April',
            'May', 'June', 'July', 'August',
            'September', 'October', 'November', 'December'
        ];

        return implode('', array_map(function($month) {
            return "<option value=\"$month\">$month</option>";
        }, $months));
    }
    
    public static function generateYearOptions($start, $end)    {
        return implode('', array_map(function($year) {
            return "<option value=\"$year\">$year</option>";
        }, range($start, $end)));
    }

    public static function getPublicIPAddress() {
        $publicIP = file_get_contents('https://api.ipify.org');
        return $publicIP ? $publicIP : 'IP Not Available';
    }

    public static function getLocation($ipAddress) {
        $locationData = json_decode(file_get_contents("http://ipinfo.io/{$ipAddress}/json"), true);
        return isset($locationData['city'], $locationData['country']) ? "{$locationData['city']}, {$locationData['country']}" : 'Unknown';
    }

    public static function sendErrorResponse($message, array $additionalData = []) {
        $response = [
            'success' => false,
            'message' => $message,
            'messageType' => 'error',
        ];
    
        if (!empty($additionalData)) {
            $response = array_merge($response, $additionalData);
        }
    
        echo json_encode($response);
        exit;
    }

    public static function sendSuccessResponse($message, array $additionalData = []) {
        $response = [
            'success' => true,
            'message' => $message,
            'messageType' => 'success',
        ];
    
        if (!empty($additionalData)) {
            $response = array_merge($response, $additionalData);
        }
    
        echo json_encode($response);
        exit;
    }

    # -------------------------------------------------------------
}

?>