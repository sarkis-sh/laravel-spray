<?php

declare(strict_types=1);

namespace src\Constants;

use src\Constants\DataType;


/**
 * Class NumericSize
 *
 * This class contains numeric size mappings for different data types.
 * 
 * @package src\Constants
 */
class NumericSize
{
    /** @var array The mapping of data types to their numeric size configuration. */
    const MAP = [
        DataType::TINY_INTEGER => [
            'max'       => 127,
            'min'       => -128,
            'precision' => 3
        ],
        DataType::SMALL_INTEGER => [
            'max'       => 32767,
            'min'       => -32768,
            'precision' => 5
        ],
        DataType::MEDIUM_INTEGER => [
            'max'       => 8388607,
            'min'       => -8388608,
            'precision' => 7
        ],
        DataType::INTEGER => [
            'max'       => 2147483647,
            'min'       => -2147483648,
            'precision' => 10
        ],
        DataType::BIG_INTEGER => [
            'max'       => 9223372036854775807,
            'min'       => -9223372036854775808,
            'precision' => 19
        ],
    ];
}
