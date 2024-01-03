<?php

declare(strict_types=1);

namespace src\Constants;

/**
 * Class representing data types.
 *
 * @package src\Constants
 */
class DataType
{
    /** Represents a string data type. */
    const STRING = 'STRING';

    /** Represents a tiny integer data type. */
    const TINY_INTEGER = 'TINY_INTEGER';

    /** Represents a small integer data type. */
    const SMALL_INTEGER = 'SMALL_INTEGER';

    /** Represents a medium integer data type. */
    const MEDIUM_INTEGER = 'MEDIUM_INTEGER';

    /** Represents an integer data type. */
    const INTEGER = 'INTEGER';

    /** Represents a big integer data type. */
    const BIG_INTEGER = 'BIG_INTEGER';

    /** Represents a bit data type. */
    const BIT = 'BIT';

    /** Represents a decimal data type. */
    const DECIMAL = 'DECIMAL';

    /** Represents a JSON data type. */
    const JSON = 'JSON';

    /** Represents a date and time data type. */
    const DATE_TIME = 'DATE_TIME';

    /** Represents a date data type. */
    const DATE = 'DATE';

    /** Represents a time data type. */
    const TIME = 'TIME';

    /** Represents a year data type. */
    const YEAR = 'YEAR';

    /** Represents a none data type. */
    const NONE = 'NONE';
}
