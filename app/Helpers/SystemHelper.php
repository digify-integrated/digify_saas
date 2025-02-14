<?php

declare(strict_types=1);

namespace App\Helpers;

use DateTime;
use DateTimeImmutable;
use Exception;

class SystemHelper
{
    /**
     * Returns a human-readable time difference string (e.g., "5 minutes ago").
     *
     * @param string $dateTime The date-time string.
     * @return string Human-readable time difference or formatted date if > 24 hours.
     */
    public static function timeElapsedString(string $dateTime): string
    {
        try {
            $date = new DateTimeImmutable($dateTime);
            $now = new DateTimeImmutable();

            $diffSeconds = $now->getTimestamp() - $date->getTimestamp();

            // Handle future dates
            if ($diffSeconds < 0) {
                return 'In the future';
            }

            // If more than 24 hours, return formatted date
            if ($diffSeconds > 86400) {
                return $date->format('M j, Y \a\t h:i:s A');
            }

            $intervals = [
                'year'   => 31536000,
                'month'  => 2592000,
                'week'   => 604800,
                'day'    => 86400,
                'hour'   => 3600,
                'minute' => 60,
                'second' => 1,
            ];

            foreach ($intervals as $label => $seconds) {
                if ($diffSeconds >= $seconds) {
                    $count = (int) floor($diffSeconds / $seconds);
                    return sprintf('%d %s ago', $count, $count > 1 ? $label . 's' : $label);
                }
            }

            return 'Just now';
        } catch (Exception $e) {
            return 'Invalid date';
        }
    }

    /**
     * Returns the difference between two dates in years and months.
     *
     * @param string $startDateTime The start date (format: "F Y").
     * @param string $endDateTime The end date (format: "F Y").
     * @return string Human-readable duration.
     */
    public static function yearMonthElapsedComparisonString(string $startDateTime, string $endDateTime): string
    {
        try {
            $startDate = DateTimeImmutable::createFromFormat('d F Y', '01 ' . $startDateTime);
            $endDate = DateTimeImmutable::createFromFormat('d F Y', '01 ' . $endDateTime);

            if (!$startDate || !$endDate) {
                throw new Exception('Error parsing dates');
            }

            $interval = $startDate->diff($endDate);
            $elapsedTime = [];

            if ($interval->y > 0) {
                $elapsedTime[] = $interval->y . ' ' . ($interval->y === 1 ? 'year' : 'years');
            }
            if ($interval->m > 0) {
                $elapsedTime[] = $interval->m . ' ' . ($interval->m === 1 ? 'month' : 'months');
            }

            return $elapsedTime ? implode(' and ', $elapsedTime) : 'Just Now';
        } catch (Exception $e) {
            return 'Error parsing dates';
        }
    }

    /**
     * Formats a given date according to a specified format.
     *
     * @param string $format Date format.
     * @param string $date Input date string.
     * @param string|null $modify Optional modification string.
     * @return string Formatted date.
     */
    public static function formatDate(string $format, string $date, ?string $modify = null): string
    {
        try {
            $dateTime = new DateTimeImmutable($date);
            return $modify ? $dateTime->modify($modify)->format($format) : $dateTime->format($format);
        } catch (Exception) {
            return 'Invalid date';
        }
    }


    /**
     * Formats a duration from seconds into human-readable time (years, months, days, etc.).
     *
     * @param int $lockDuration Duration in seconds.
     * @return array Human-readable duration parts.
     */
    public static function formatDuration(int $lockDuration): array
    {
        $durationParts = [];
        $timeUnits = [
            ['year', 31536000],
            ['month', 2592000],
            ['day', 86400],
            ['hour', 3600],
            ['minute', 60],
            ['second', 1]
        ];

        foreach ($timeUnits as [$unit, $seconds]) {
            $value = (int) floor($lockDuration / $seconds);
            $lockDuration %= $seconds;

            if ($value > 0) {
                $durationParts[] = number_format($value) . ' ' . $unit . ($value > 1 ? 's' : '');
            }
        }

        return $durationParts ?: ['less than a second'];
    }

    /**
     * Returns the default return value based on a type.
     *
     * @param string $type The type of return value.
     * @param string $systemDate Default system date.
     * @param string $systemTime Default system time.
     * @return string|null The corresponding value.
     */
    public static function getDefaultReturnValue(string $type, string $systemDate, string $systemTime): ?string
    {
        return match ($type) {
            'default'      => $systemDate,
            'default time' => $systemTime,
            'na', 'complete', 'encoded', 'date time' => 'N/A',
            'empty', 'attendance empty', 'summary' => null,
            default        => null,
        };
    }

    /**
     * Retrieves the public IP address.
     *
     * @return string The public IP address or error message.
     */
    public static function getPublicIPAddress(): string
    {
        return @file_get_contents('https://api.ipify.org') ?: 'IP Not Available';
    }

    /**
     * Fetches the location based on an IP address.
     *
     * @param string $ipAddress The IP address.
     * @return string Location (City, Country) or 'Unknown'.
     */
    public static function getLocation(string $ipAddress): string
    {
        $data = @json_decode(file_get_contents("http://ipinfo.io/{$ipAddress}/json"), true);
        return $data['city'] ?? 'Unknown' . ', ' . ($data['country'] ?? 'Unknown');
    }

    /**
     * Sends an error response as JSON and terminates the script.
     *
     * @param string $message Error message.
     * @param array $additionalData Additional response data.
     */
    public static function sendErrorResponse(string $message, array $additionalData = []): void
    {
        echo json_encode(array_merge(['success' => false, 'message' => $message, 'messageType' => 'error'], $additionalData));
        exit;
    }

    /**
     * Sends a success response as JSON and terminates the script.
     *
     * @param string $message Success message.
     * @param array $additionalData Additional response data.
     */
    public static function sendSuccessResponse(string $message, array $additionalData = []): void
    {
        echo json_encode(array_merge(['success' => true, 'message' => $message, 'messageType' => 'success'], $additionalData));
        exit;
    }
}
