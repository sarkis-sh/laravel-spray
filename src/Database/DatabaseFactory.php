<?php

declare(strict_types=1);

namespace src\Database;

use Exception;
use src\Config\DBConfig;
use src\Database\Drivers\MySQLDriver;
use src\Models\Database\Structure\Database;


/**
 * Class DatabaseFactory
 *
 * A factory class for creating database instances based on the configured database type.
 * 
 * @package src\Database
 */
class DatabaseFactory
{
    /**
     * Creates a database instance based on the configured database type.
     *
     * @return Database The created database instance.
     *
     * @throws Exception If the configured database type is unsupported.
     */
    public static function create(): Database
    {
        $databaseType = DBConfig::$dbConnection;
        switch ($databaseType) {
            case 'mysql':
                return (new MySQLDriver())->getDatabase();
            default:
                throw new Exception("Unsupported database type: $databaseType");
        }
    }
}
