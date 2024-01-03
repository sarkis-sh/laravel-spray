<?php

declare(strict_types=1);

namespace src\Constants\Postman;


/**
 * Class RawOptions
 *
 * This class contains constants for different options used for the RAW body mode in Postman requests.
 * 
 * @package src\Constants\Postman
 */
class RawOptions
{
    /** @var string The TEXT option for the RAW body mode. */
    const TEXT          = 'text';   // unused

    /** @var string The JAVASCRIPT option for the RAW body mode. */
    const JAVA_SCRIPT   = 'javascript';   // unused

    /** @var string The JSON option for the RAW body mode. */
    const JSON          = 'json';

    /** @var string The HTML option for the RAW body mode. */
    const HTML          = 'html';  // unused

    /** @var string The XML option for the RAW body mode. */
    const XML           = 'xml';  // unused
}
