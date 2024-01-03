<?php

declare(strict_types=1);

namespace src\Models\Database\Structure;

use Exception;
use src\Config\IgnoreArray;
use src\Helpers\ArrayHelper;
use src\Helpers\NamingHelper;
use src\Models\Database\Relation\BelongsTo;
use src\Models\Database\Relation\HasMany;


/**
 * Represents a database containing multiple tables.
 *
 * This class encapsulates information about a database, including an array of tables and methods for
 * establishing relationships between the tables, adding relationships, and generating validation attributes.
 *
 * @package src\Models\Database\Structure
 */
class Database
{
    /** @var Table[] An array of Table objects. */
    private array $tables;

    /** @param Table[] $tables An array of Table objects. */
    public function __construct(array $tables)
    {
        $this->tables = $tables;
        $this->addTableRealtions();
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new Exception("Property $name does not exist.");
    }

    /**
     * Add relationships between the tables in the database.
     * This function establishes relationships such as BelongsTo and HasMany between the tables' foreign keys.
     * 
     * @return void
     */
    private function addTableRealtions(): void
    {
        foreach ($this->tables as $table) {
            // Check if the table is not pivot
            if (!$table->isPivot) {
                $foreignKeysIndex = $table->foreignKeysIndex;
                $tableName = $table->tableName;

                foreach ($foreignKeysIndex as $foreignKey) {
                    $referencedTable = $this->tables[$foreignKey->foreignKey->referencedTable];

                    $referencedTableName = $referencedTable->tableName;
                    $columnName = $foreignKey->columnName;
                    $referencedColumn = $foreignKey->foreignKey->referencedColumn;

                    $table->addRelation(new BelongsTo(
                        $tableName,
                        $referencedTableName,
                        $columnName,
                        $referencedColumn
                    ));

                    $referencedTable->addRelation(new HasMany(
                        $referencedTableName,
                        $tableName,
                        $columnName,
                        $referencedColumn
                    ));
                }
            }
        }
    }

    /**
     * Get the validation attributes array as a formatted string, optionally merging with old validation attributes.
     *
     * @param array $oldValidationAttributes An array containing the old validation attributes to merge with.
     * @return string The formatted string representation of the validation attributes array.
     */
    public function getValidationAttributesArray(array $oldValidationAttributes = [], string $lang = 'en'): string
    {
        $columnNamesArray = [];
        $columnNames = array_reduce($this->tables, function ($carry, $table) use (&$columnNamesArray, $oldValidationAttributes, $lang) {
            foreach ($table->columns as $column) {
                $columnName = $column->columnName;
                if (!in_array($columnName, $columnNamesArray) && !in_array($columnName, IgnoreArray::EXTRA_COLUMNS)) {
                    $columnNamesArray[] = $columnName;
                    if (isset($oldValidationAttributes[$columnName]) && !empty(trim($oldValidationAttributes[$columnName], '\t\n\r\0\x0B\','))) {
                        $carry .= "'$columnName' => " . $oldValidationAttributes[$columnName] . "\n";
                    } else {
                        if ($lang == 'en') {
                            $carry .= "'$columnName' => '" . NamingHelper::snakeCaseToTitleCase($columnName) . "',\n";
                        } else {
                            $carry .= "'$columnName' => '',\n";
                        }
                    }
                }
            }
            return $carry;
        }, '');
        return ArrayHelper::formatKeyValuePairs($columnNames, "\t\t");
    }

    /**
     * Get the table with the specified table name.
     *
     * @param string $tableName The name of the table to retrieve.
     *
     * @return Table|null The table object if found, or null if not found.
     */
    public function getTable(string $tableName)
    {
        foreach ($this->tables as $table) {
            if ($table->tableName == $tableName) {
                return $table;
            }
        }
        return null;
    }

    /**
     * Compare the current Database object with another Database object for equality.
     *
     * @param Database $otherObject The Database object to compare.
     *
     * @return bool True if the objects are equal, false otherwise.
     */
    public function equals(Database $otherObject)
    {
        // Check if the number of tables is different
        if (count($this->tables) !== count($otherObject->tables)) {
            return false;
        }

        // Check equality for each table
        foreach ($this->tables as $table) {
            if (!$this->hasEquivalentTable($table, $otherObject->tables)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if an equivalent table exists in the given array of tables.
     *
     * @param Table $table The Table object to check.
     * @param Table[] $tablesArray The array of Table objects to search.
     *
     * @return bool True if an equivalent table exists, false otherwise.
     */
    private function hasEquivalentTable(Table $table, array $tablesArray)
    {
        foreach ($tablesArray as $otherTable) {
            if ($table->equals($otherTable)) {
                return true;
            }
        }
        return false;
    }
}
