<?php

declare(strict_types=1);

namespace src\Config\Paths;


/**
 * Class OutPaths
 *
 * This class contains the paths for output files.
 * 
 * @package src\Config\Paths
 */
class OutPaths
{
    /** @var string The file path for the projects configuration file. */
    const USER_PROJECTS_FILE    = __DIR__ . '\\..\\..\\..\\user_projects.json';

    /** @var string The output directory for database cach files. */
    const DB_CACH_DIRECTORY     = __DIR__ . '\\..\\..\\..\\out\\cach\\';

    /** @var string The output directory for Postman files. */
    const POSTMAN_OUT_DIRECTORY = __DIR__ . '\\..\\..\\..\\out\\postman\\';

    /** @var string The file path for the error log file */
    const ERROR_LOG_FILE        = '\\spray_errors.log';

    /** @var string The file path for the generation log file */
    const GENERATION_LOG_FILE   = '\\spray_generation.log';
}
