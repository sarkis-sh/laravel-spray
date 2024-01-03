<?php

declare(strict_types=1);

namespace src\Config\Paths;

/**
 * Class Path
 *
 * This class contains the paths for Resource files.
 * 
 * @package src\Config\Paths
 */
class ResourcePaths
{
    /** @var string The directory path for controllers. */
    const CONTROLLERS_RESOURCE_DIRECTORY = __DIR__ . '\\..\\..\\..\\resources\\Controllers\\';

    /** @var string The directory path for traits. */
    const TRAITS_RESOURCE_DIRECTORY = __DIR__ . '\\..\\..\\..\\resources\\Traits\\';

    /** @var string The directory path for language files. */
    const LANG_RESOURCE_DIRECTORY = __DIR__ . '\\..\\..\\..\\resources\\Lang\\';

    /** @var string The directory path for Middleware files. */
    const MIDDLEWARE_RESOURCE_DIRECTORY = __DIR__ . '\\..\\..\\..\\resources\\Middleware\\';

    /** @var string The file path for the handler resource. */
    const HANDLER_RESOURCE_FILE = __DIR__ . '\\..\\..\\..\\resources\\Handler.php';

    /** @var string The file path for the generic resource. */
    const GENERIC_RESOURCE_FILE = __DIR__ . '\\..\\..\\..\\resources\\GenericResource.php';

    /** @var string The file path for the generic response. */
    const GENERIC_RESPONSE_FILE = __DIR__ . '\\..\\..\\..\\resources\\GenericRequest.php';

    /** @var string The file path for the generic service. */
    const GENERIC_SERVICE_FILE = __DIR__ . '\\..\\..\\..\\resources\\GenericService.php';

    /** @var string The file path for the generic model. */
    const GENERIC_MODEL_FILE = __DIR__ . '\\..\\..\\..\\resources\\GenericModel.php';

    /**  @var array The mapping of resource files to their corresponding base directories. */
    const RESOURCE_FILES = [
        self::CONTROLLERS_RESOURCE_DIRECTORY    => LaravelPaths::CONTROLLER_BASE_DIRECTORY,

        self::TRAITS_RESOURCE_DIRECTORY         => LaravelPaths::TRAIT_BASE_DIRECTORY,
        self::LANG_RESOURCE_DIRECTORY           => LaravelPaths::LANG_BASE_DIRECTORY,
        self::HANDLER_RESOURCE_FILE             => LaravelPaths::HANDLER_BASE_DIRECTORY,
        self::MIDDLEWARE_RESOURCE_DIRECTORY     => LaravelPaths::MIDDLEWARE_BASE_DIRECTORY,

        self::GENERIC_RESOURCE_FILE             => LaravelPaths::RESOURCE_BASE_DIRECTORY,
        self::GENERIC_RESPONSE_FILE             => LaravelPaths::REQUEST_BASE_DIRECTORY,
        self::GENERIC_SERVICE_FILE              => LaravelPaths::SERVICE_BASE_DIRECTORY,
        self::GENERIC_MODEL_FILE                => LaravelPaths::MODEL_BASE_DIRECTORY
    ];
}
