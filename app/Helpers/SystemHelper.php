<?php

declare(strict_types=1);

namespace App\Helpers;

use DateTimeImmutable;
use DateTime;
use Exception;

class SystemHelper
{
    public static function timeElapsedString(string $dateTime): string
    {
        $timestamp = strtotime($dateTime);
        if ($timestamp === false) {
            return 'Invalid date';
        }

        $diffSeconds = time() - $timestamp;
        if ($diffSeconds > 86400) {
            return date('M j, Y \a\t h:i:s A', $timestamp);
        }

        $intervals = [
            31536000 => 'year',
            2592000  => 'month',
            604800   => 'week',
            86400    => 'day',
            3600     => 'hour',
            60       => 'minute',
            1        => 'second'
        ];

        foreach ($intervals as $seconds => $label) {
            $count = intdiv($diffSeconds, $seconds);
            if ($count > 0) {
                return sprintf('%d %s ago', $count, $label . ($count > 1 ? 's' : ''));
            }
        }

        return 'Just Now';
    }

    public static function yearMonthElapsedComparisonString(string $startDateTime, string $endDateTime): string
    {
        try {
            $startDate = new DateTimeImmutable('01 ' . $startDateTime);
            $endDate = new DateTimeImmutable('01 ' . $endDateTime);
        } catch (Exception $e) {
            return 'Error parsing dates';
        }

        $interval = $startDate->diff($endDate);
        $elapsed = [];

        if ($interval->y > 0) {
            $elapsed[] = "{$interval->y} year" . ($interval->y > 1 ? 's' : '');
        }
        if ($interval->m > 0) {
            $elapsed[] = "{$interval->m} month" . ($interval->m > 1 ? 's' : '');
        }

        return $elapsed ? implode(' and ', $elapsed) : 'Just Now';
    }

    public static function formatDate(string $format, string $date, ?string $modify = null): string
    {
        try {
            $dateTime = new DateTimeImmutable($date);
            if ($modify) {
                $dateTime = $dateTime->modify($modify);
            }
            return $dateTime->format($format);
        } catch (Exception $e) {
            return 'Invalid date';
        }
    }

    public static function formatDuration(int $seconds): string
    {
        if ($seconds <= 0) {
            return 'Less than a second';
        }

        $timeUnits = [
            'year'   => 60 * 60 * 24 * 365,
            'month'  => 60 * 60 * 24 * 30,
            'day'    => 60 * 60 * 24,
            'hour'   => 60 * 60,
            'minute' => 60,
            'second' => 1
        ];

        $duration = [];
        foreach ($timeUnits as $unit => $unitSeconds) {
            $value = intdiv($seconds, $unitSeconds);
            $seconds %= $unitSeconds;

            if ($value > 0) {
                $duration[] = "$value $unit" . ($value > 1 ? 's' : '');
            }
        }

        return implode(', ', $duration);
    }

    public static function getDefaultReturnValue(string $type, string $systemDate, string $systemTime): ?string
    {
        return match ($type) {
            'default'        => $systemDate,
            'default time'   => $systemTime,
            'na', 'complete', 'encoded', 'date time' => 'N/A',
            'empty', 'attendance empty', 'summary'  => null,
            default          => null,
        };
    }

    public static function getDefaultImage(string $type): string
    {
        $defaultImages = [
            'profile'               => DEFAULT_AVATAR_IMAGE,
            'login background'      => DEFAULT_BG_IMAGE,
            'login logo'            => DEFAULT_LOGIN_LOGO_IMAGE,
            'menu logo'             => DEFAULT_MENU_LOGO_IMAGE,
            'module icon'           => DEFAULT_MODULE_ICON_IMAGE,
            'favicon'               => DEFAULT_FAVICON_IMAGE,
            'company logo'          => DEFAULT_COMPANY_LOGO,
            'id placeholder front'  => DEFAULT_ID_PLACEHOLDER_FRONT,
            'app module logo'       => DEFAULT_APP_MODULE_LOGO,
            'upload placeholder'    => DEFAULT_UPLOAD_PLACEHOLDER,
        ];

        return $defaultImages[$type] ?? DEFAULT_PLACEHOLDER_IMAGE;
    }

    public static function checkImage(?string $image, string $type): string
    {
        $imagePath = str_replace('./apps/', '../../../../apps/', $image ?? '');
        return empty($image) || (!file_exists($imagePath) && !file_exists($image))
            ? self::getDefaultImage($type)
            : $image;
    }

    public static function getFileExtensionIcon(string $type): string
    {
        $fileIcons = [
            'ai'    => 'img-file-ai.svg',
            'doc'   => 'img-file-doc.svg',
            'docx'  => 'img-file-doc.svg',
            'jpeg'  => 'img-file-img.svg',
            'jpg'   => 'img-file-img.svg',
            'png'   => 'img-file-img.svg',
            'gif'   => 'img-file-img.svg',
            'pdf'   => 'img-file-pdf.svg',
            'ppt'   => 'img-file-ppt.svg',
            'pptx'  => 'img-file-ppt.svg',
            'rar'   => 'img-file-rar.svg',
            'txt'   => 'img-file-txt.svg',
            'xls'   => 'img-file-xls.svg',
            'xlsx'  => 'img-file-xls.svg',
        ];

        return './assets/images/file-icon/' . ($fileIcons[$type] ?? 'img-file-img.svg');
    }

    public static function getFormatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $bytes = max($bytes, 0);
        $pow = $bytes ? floor(log($bytes, 1024)) : 0;

        return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
    }

    public static function generateMonthOptions(): string
    {
        return implode('', array_map(fn($month) => "<option value=\"$month\">$month</option>", [
            'January', 'February', 'March', 'April',
            'May', 'June', 'July', 'August',
            'September', 'October', 'November', 'December'
        ]));
    }

    public static function generateYearOptions(int $start, int $end): string
    {
        return implode('', array_map(fn($year) => "<option value=\"$year\">$year</option>", range($start, $end)));
    }
}
