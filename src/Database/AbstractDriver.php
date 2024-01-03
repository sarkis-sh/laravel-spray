<?php

declare(strict_types=1);

namespace src\Database;

use src\Models\Database\Structure\Database;


/**
 * Class AbstractDriver
 *
 * This abstract class represents a database driver.
 * 
 * @package src\Database
 */
abstract class AbstractDriver
{
    /**
     * Get the reflected database
     * 
     * @return Database The reflected database.
     */
    abstract function getDatabase(): Database;
}
