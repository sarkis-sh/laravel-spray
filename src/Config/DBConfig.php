<?php

declare(strict_types=1);

namespace src\Config;

/**
 * Class DBConfig
 *
 * This class represents the configuration for the database connection.
 * 
 * @package src\Config
 */
class DBConfig
{
    /** @var string|null The database connection name. */
    public static ?string $dbConnection;

    /** @var string|null The database hostname. */
    public static ?string $dbHostname;

    /** @var int|null The database port number. */
    public static ?int $dbPort;

    /** @var string|null The database name. */
    public static ?string $dbDatabase;

    /** @var string|null The database username. */
    public static ?string $dbUsername;

    /** @var string|null The database password. */
    public static ?string $dbPassword;

    /** @var string|null The database socket. */
    public static ?string $dbSocket;

    /**
     * Initialize the DBConfig.
     *
     * This method initializes the database configuration by retrieving values from environment variables.
     * It sets the values for the database connection, hostname, port, database name, username, password, and socket.
     *
     * @param string|null $dbSocket The database socket (optional).
     * 
     * @return void 
     */
    public static function init(?string $dbSocket = null): void
    {
        self::$dbConnection   = getenv('DB_CONNECTION');
        self::$dbHostname     = getenv('DB_HOST');
        self::$dbPort         = intval(getenv('DB_PORT'));
        self::$dbDatabase     = getenv('DB_DATABASE');
        self::$dbUsername     = getenv('DB_USERNAME');
        self::$dbPassword     = getenv('DB_PASSWORD');
        self::$dbSocket       = $dbSocket;
    }
}
