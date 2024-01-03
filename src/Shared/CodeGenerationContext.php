<?php

declare(strict_types=1);

namespace src\Shared;

use src\Config\Paths\LaravelPaths;
use src\Config\Paths\OutPaths;
use src\Models\Database\Structure\Table;
use src\Models\Database\Structure\Database;
use src\Utils\FileManager;

/**
 * Represents the context for code generation.
 *
 * This class holds static properties that represent the context for code generation, including information
 * about the database, selected table, class name, variable names, Laravel root directory, and selected APIs.
 *
 * @package src\Shared
 */
class CodeGenerationContext
{
    /** @var Database The database used for code generation. */
    public static Database $database;

    /** @var string The name of the project. */
    public static string $projectName;

    /** @var Table The selected table for code generation. */
    public static Table $selectedTable;

    /** @var Table[] The selected table for code generation. */
    public static array $selectedTables;

    /** @var string The root directory of the Laravel project. */
    public static string $laravelRootDirectory;

    /** @var array The selected APIs for code generation. */
    public static array $selectedAPIs;

    /** @var string The path to the previous database version file. */
    public static string $previousDBVersionPath;

    /** @var string The path to the latest database version file. */
    public static string $latestDBVersionPath;

    /** @var string The directory path for log files. */
    public static string $logDirectory;

    /**
     * Sets the Laravel root directory and initializes related variables.
     *
     * @param string $laravelRootDirectory The path to the Laravel root directory.
     * @return void
     */
    public static function setLaravelRootDirectory(string $laravelRootDirectory): void
    {
        self::$laravelRootDirectory = $laravelRootDirectory;
        self::$projectName = pathinfo(self::$laravelRootDirectory)['filename'];

        $projectDBCacheDirectory = OutPaths::DB_CACH_DIRECTORY . self::$projectName;

        self::$logDirectory = self::$laravelRootDirectory . LaravelPaths::LOG_DIRECTORY;

        // Make DB cach directory for project If not already exist
        FileManager::makeDirectory($projectDBCacheDirectory);

        // Make log directory for project If not already exist
        FileManager::makeDirectory(self::$logDirectory);

        self::$previousDBVersionPath = $projectDBCacheDirectory . "\\" . self::$projectName . "_DB_previous.dat";
        self::$latestDBVersionPath = $projectDBCacheDirectory . "\\" . self::$projectName . "_DB_latest.dat";
    }

    /**
     * Sets the database for code generation.
     *
     * @param Database $database The database instance.
     * 
     * @return void
     */
    public static function setDatabase(Database $database): void
    {
        self::$database = $database;
    }

    /**
     * Initializes the code generation context with the selected table and APIs.
     *
     * @param Table $selectedTable The selected table for code generation.
     * @param array $selectedAPIs The selected APIs for code generation.
     * 
     * @return void
     */
    public static function init(array $selectedTables = [], $selectedAPIs = []): void
    {
        self::$selectedTables = $selectedTables;
        self::$selectedAPIs = $selectedAPIs;
    }
}
