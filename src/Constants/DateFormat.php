<?php

declare(strict_types=1);

namespace src\Constants;

use src\Constants\DataType;


/**
 * Class DateFormat
 *
 * This class contains constants for different date and time formats used in the application.
 * 
 * @package src\Constants
 */
class DateFormat
{
    /*
    |--------------------------------------------------------------------------
    | Date and time formats
    |--------------------------------------------------------------------------
    | Y - Year with 4 digits (e.g., 2023)
    | y - Year with 2 digits (e.g., 23)
    | m - Month with leading zeros (01 to 12)
    | n - Month without leading zeros (1 to 12)
    | d - Day of the month with leading zeros (01 to 31)
    | j - Day of the month without leading zeros (1 to 31)
    | D - Short textual representation of the day (e.g., Mon, Tue)
    | l - Full textual representation of the day (e.g., Monday, Tuesday)
    | M - Short textual representation of the month (e.g., Jan, Feb)
    | F - Full textual representation of the month (e.g., January, February)
    | H - Hour in 24-hour format with leading zeros (00 to 23)
    | h - Hour in 12-hour format with leading zeros (01 to 12)
    | i - Minutes with leading zeros (00 to 59)
    | s - Seconds with leading zeros (00 to 59)
    | A - Uppercase "AM" or "PM"
    | a - Lowercase "am" or "pm"
    | U - Unix timestamp (seconds since January 1, 1970)
    | r - RFC 2822 formatted date (e.g., Thu, 21 Oct 2023 16:21:07 +0000)
    |
    */


    /** @var array The mapping of data types to date and time formats. */
    const MAP = [
        DataType::DATE_TIME   => 'Y-m-d H:i:s',
        DataType::DATE        => 'Y-m-d',
        DataType::TIME        => 'H:i:s',
        DataType::YEAR        => 'Y',
    ];
}
