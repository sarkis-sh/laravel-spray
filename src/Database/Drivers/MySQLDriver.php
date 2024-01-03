<?php

declare(strict_types=1);

namespace src\Database\Drivers;

use mysqli;
use src\Config\DBConfig;
use src\Database\AbstractDriver;
use src\Models\Database\Structure\Database;


/**
 * Class MySQLDriver
 *
 * This class represents a MySQL database driver and extends the AbstractDriver class.
 * 
 * @package src\Database\Drivers
 */
class MySQLDriver extends AbstractDriver
{

    /** @var mysqli The MySQL database connection. */
    private mysqli $connection;


    /**
     * Establishes a connection to the MySQL database using the provided configuration.
     */
    public function __construct()
    {
        $this->connection = new mysqli(
            DBConfig::$dbHostname,
            DBConfig::$dbUsername,
            DBConfig::$dbPassword,
            DBConfig::$dbDatabase,
            DBConfig::$dbPort,
            DBConfig::$dbSocket
        );
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function getDatabase(): Database
    {
        $database =  (new MySQLReflector($this->connection))->reflect();

        $this->connection->close();

        return $database;
    }
}
