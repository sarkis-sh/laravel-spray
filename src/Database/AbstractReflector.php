<?php

declare(strict_types=1);

namespace src\Database;

use src\Constants\DataType;
use src\Models\Database\Structure\Database;


/**
 * Class AbstractReflector
 *
 * This abstract class represents a reflector for database structure.
 *
 * @package src\Database
 */
abstract class AbstractReflector
{
    /**
     * Reflect the database structure in the database models.
     * 
     * @return Database The reflected database.
     */
    abstract function reflect(): Database;

    /**
     * Get database information schema
     * 
     * @return array The array contains all tables with their respective column information, 
     * and the resulting array is grouped by table name.
     */
    abstract function getDatabaseInfo(): array;

    /**
     * Map the original data type to the standardized data type.
     * 
     * @param string $dataType the original SQL datatype
     * 
     * @return string the reflected datatype.
     */
    abstract function getReflectedDataType(string $dataType): string;
}
