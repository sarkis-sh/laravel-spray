<?php

declare(strict_types=1);

namespace src\Models\Database\Structure;

use Exception;


/**
 * Represents a foreign key in a database table.
 *
 * This class encapsulates information about a foreign key, including the name of the referenced table
 * and the name of the referenced column.
 *
 * @package src\Models\Database\Structure
 */
class ForeignKey
{
    /** @var string|null $referencedTable The name of the referenced table. */
    private ?string $referencedTable;

    /** @var string|null $referencedColumn The name of the referenced column. */
    private ?string $referencedColumn;



    /**
     * @param string|null $referencedTable The name of the referenced table.
     * @param string|null $referencedColumn The name of the referenced column.
     */
    public function __construct(?string $referencedTable,  ?string $referencedColumn)
    {
        $this->referencedTable = $referencedTable;
        $this->referencedColumn = $referencedColumn;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new Exception("Property $name does not exist.");
    }
}
