<?php

declare(strict_types=1);

namespace src\Constants\Postman;

/**
 * Class BodyMode
 *
 * This class contains constants for different body modes used in Postman requests.
 * 
 * @package src\Constants\Postman
 */
class BodyMode
{
    /** @var string The URLENCODED body mode. */
    const URLENCODED    = 'urlencoded';

    /** @var string The FORM_DATA body mode. */
    const FORM_DATA     = 'formdata';

    /** @var string The RAW body mode. */
    const RAW           = 'raw';
}
