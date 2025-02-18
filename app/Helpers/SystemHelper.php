<?php

declare(strict_types=1);

namespace App\Helpers;

use DateTimeImmutable;
use Exception;

class SystemHelper
{
    /**
     * Returns a human-readable time difference string (e.g., "5 minutes ago").
     * If the time difference is greater than 24 hours, it returns a formatted date.
     *
     * @param string $dateTime The date-time string to compare against the current time
     * @return string A human-readable time difference or a formatted date if > 24 hours
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
     * Returns the difference between two dates in years and months in a human-readable format.
     * The dates are expected to be in the format "F Y" (e.g., "Feb 2025").
     *
     * @param string $startDateTime The start date in "F Y" format
     * @param string $endDateTime The end date in "F Y" format
     * @return string A human-readable duration, such as "2 years and 3 months"
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

            // Add years and months to the duration
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
     * Formats a given date according to the specified format.
     * Optionally, you can modify the date before formatting it.
     *
     * @param string $format The desired date format (e.g., "Y-m-d H:i:s")
     * @param string $date The date string to format
     * @param string|null $modify Optional modification string (e.g., "+1 day")
     * @return string The formatted date string or 'Invalid date' if parsing fails
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
     * Formats a duration from seconds into a human-readable time format (years, months, days, etc.).
     * Returns an array of formatted parts (e.g., ["1 year", "2 months"]).
     *
     * @param int $lockDuration Duration in seconds
     * @return array An array of human-readable duration parts (e.g., ["1 year", "2 months"])
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

            // Add the unit to the duration parts if the value is greater than 0
            if ($value > 0) {
                $durationParts[] = number_format($value) . ' ' . $unit . ($value > 1 ? 's' : '');
            }
        }

        return $durationParts ?: ['less than a second'];
    }

    /**
     * Returns the default return value based on a given type.
     * Useful for setting default system values based on context.
     *
     * @param string $type The type of value to return (e.g., 'default', 'default time', etc.)
     * @param string $systemDate Default system date
     * @param string $systemTime Default system time
     * @return string|null The corresponding default value, or null if not applicable
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
     * Sends an error response as JSON and terminates the script.
     * Useful for API endpoints that need to return error information.
     *
     * @param string $message The error message to include in the response
     * @param array $additionalData Additional data to include in the response
     */
    public static function sendErrorResponse(string $message, array $additionalData = []): void
    {
        echo json_encode(array_merge(['success' => false, 'message' => $message, 'messageType' => 'error'], $additionalData));
        exit;
    }

    /**
     * Sends a success response as JSON and terminates the script.
     * Useful for API endpoints that need to return success information.
     *
     * @param string $message The success message to include in the response
     * @param array $additionalData Additional data to include in the response
     */
    public static function sendSuccessResponse(string $message, array $additionalData = []): void
    {
        echo json_encode(array_merge(['success' => true, 'message' => $message, 'messageType' => 'success'], $additionalData));
        exit;
    }
}