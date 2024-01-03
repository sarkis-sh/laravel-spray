<?php

declare(strict_types=1);

namespace src\Config\Paths;


/**
 * Class LaravelPaths
 *
 * This class manages the directories where the final files of code generation will be written
 * and other laravel paths supporting code generation
 *
 * @package src\Config\Paths
 */
class LaravelPaths
{
    /** @var string The base directory for generated controllers. */
    const CONTROLLER_BASE_DIRECTORY = 'App\\Http\\Controllers';

    /** @var string The base directory for generated middlewares. */
    const MIDDLEWARE_BASE_DIRECTORY = 'App\\Http\\Middleware';

    /**  @var string The base directory for generated resources. */
    const RESOURCE_BASE_DIRECTORY = 'App\\Http\\Resources';

    /** @var string The base directory for generated factories. */
    const FACTORY_BASE_DIRECTORY = 'Database\\factories';

    /** @var string The base directory for generated requests. */
    const REQUEST_BASE_DIRECTORY = 'App\\Http\\Requests';

    /** @var string The base directory for generated services. */
    const SERVICE_BASE_DIRECTORY = 'App\\Services';

    /** @var string The base directory for generated models. */
    const MODEL_BASE_DIRECTORY = 'App\\Models';

    /** @var string The base directory for generated traits. */
    const TRAIT_BASE_DIRECTORY = 'App\\Traits';

    /** @var string The base directory for language files. */
    const LANG_BASE_DIRECTORY = 'resources\\lang';

    /** @var string The base directory for exception handlers. */
    const HANDLER_BASE_DIRECTORY = 'App\\Exceptions';

    /** @var string The file path for the Laravel application class. */
    const APPLICATION_FILE_PATH = 'vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php';

    /**  @var string The base path for API routes. */
    const ROUTES_API_BASE_PATH = 'routes\\api.php';

    /** @var string The file path for the environment configuration file. */
    const ENV_FILE_PATH = '.env';

    /** @var string The directory path for log files. */
    const LOG_DIRECTORY = 'storage\\logs';
}
