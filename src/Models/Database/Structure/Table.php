<?php

declare(strict_types=1);

namespace src\Models\Database\Structure;

use Exception;
use src\Config\IgnoreArray;
use src\Models\Database\Relation\Relation;
use src\Helpers\ArrayHelper;

/**
 * Represents a database table.
 *
 * This class encapsulates information about a database table, including its name, columns, foreign keys, and relations.
 *
 * @package src\Models\Database\Structure
 */
class Table
{
    /** @var string $tableName The name of the table. */
    public string $tableName;

    /** @var Column[] $columns An array of Column objects representing the columns of the table. */
    private array $columns;

    /** @var array $foreignKeysIndex An associative array that stores foreign keys indexed by column name. */
    private array $foreignKeysIndex;

    /** @var int $extraColumnsCount The count of extra columns in the table (id, created_at, updated_at, deleted_at). */
    private int $extraColumnsCount;

    /** @var bool Indicates whether the table is a pivot table. */
    private bool $isPivot;

    /** @var Relation[] An array of Relation objects representing the relations associated with the table. */
    private array $relations = [];

    /** @var bool Indicates whether to use the created_at column. */
    private bool $use_created_at;

    /** @var bool Indicates whether to use the updated_at column. */
    private bool $use_updated_at;

    /**
     * @param string $tableName The name of the table.
     * @param Column[] $columns An array of Column objects representing the columns of the table.
     * @param array $foreignKeysIndex An associative array that stores foreign keys indexed by column name.
     * @param int $extraColumnsCount The count of extra columns in the table (id, created_at, updated_at, deleted_at).
     */
    public function __construct(
        string $tableName,
        array $columns,
        array $foreignKeysIndex,
        int $extraColumnsCount
    ) {
        $this->tableName = $tableName;
        $this->columns = $columns;
        $this->use_created_at = in_array('created_at', array_column($columns, 'columnName'));
        $this->use_updated_at = in_array('updated_at', array_column($columns, 'columnName'));
        $this->foreignKeysIndex = $foreignKeysIndex;
        $this->extraColumnsCount = $extraColumnsCount;
        $this->isPivot = sizeof($this->foreignKeysIndex) == (sizeof($this->columns) - $this->extraColumnsCount);
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new Exception("Property $name does not exist.");
    }
    /**
     * Add a relation to the table.
     *
     * @param Relation $relation The Relation object representing the relation to be added.
     * @return void
     */
    public function addRelation(Relation $relation): void
    {
        $this->relations[] = $relation;
    }


    /**
     * Get Laravel validation rules for creating a record with the table's columns as a formatted string.
     *
     * @param array $oldValidation An optional array containing old validation rules to include in the result.
     *
     * @return string The formatted validation rules string.
     */
    public function getStoreValidationRules(array $oldValidation = []): string
    {
        $storeValidationString = "";

        foreach ($this->columns as $column) {
            if (!in_array($column->columnName, IgnoreArray::FORM_REQUEST_COLUMNS)) {
                if (!$column->isChanged() && isset($oldValidation[$column->columnName])) {
                    $storeValidationString .= "'$column->columnName' => " . $oldValidation[$column->columnName] . "\n";
                } else {
                    $storeValidationString .= $column->getStoreValidationRules();
                }
            }
        }
        return ArrayHelper::formatKeyValuePairs($storeValidationString);
    }

    /**
     * Get Laravel validation rules for creating a record with the table's columns in array as a formatted string.
     *
     * @param array $oldValidation An optional array containing old validation rules to include in the result.
     * 
     * @return string
     */
    public function getBulkStoreValidationRules(array $oldValidation = []): string
    {
        $bulkStoreValidationString = "'list' => 'required|array',\n";

        foreach ($this->columns as $column) {
            if (!in_array($column->columnName, IgnoreArray::FORM_REQUEST_COLUMNS)) {
                if (!$column->isChanged() && isset($oldValidation["list.*.$column->columnName"])) {
                    $bulkStoreValidationString .= "'list.*.$column->columnName' => " . $oldValidation["list.*.$column->columnName"] . "\n";
                } else {
                    $bulkStoreValidationString .= $column->getBulkStoreValidationRules();
                }
            }
        }
        return ArrayHelper::formatKeyValuePairs($bulkStoreValidationString);
    }

    /**
     * Get Laravel validation rules for deleting a list of records.
     * 
     * @return string
     */
    public function getBulkDeleteValidationRules()
    {
        $bulkDeleteValidationString = "\t\t\t'ids'    =>  'required|array',\n\t\t\t'ids.*'  =>  'required|integer|exists:$this->tableName,id',";

        return $bulkDeleteValidationString;
    }

    /**
     * Get Laravel validation rules for updating a record with the table's columns as a formatted string.
     *
     * @param array $oldValidation An optional array containing old validation rules to include in the result.
     * 
     * @return string
     */
    public function getUpdateValidationRules(array $oldValidation = []): string
    {
        $updateValidationString = "";

        foreach ($this->columns as $column) {
            if (!in_array($column->columnName, IgnoreArray::FORM_REQUEST_COLUMNS)) {
                if (!$column->isChanged() && isset($oldValidation[$column->columnName])) {
                    $updateValidationString .= "'$column->columnName' => " . $oldValidation[$column->columnName] . "\n";
                } else {
                    $updateValidationString .= $column->getUpdateValidationRules();
                }
            }
        }
        return ArrayHelper::formatKeyValuePairs($updateValidationString);
    }

    /**
     * Get the faker array for the table as a formatted string.
     *
     * @param array $oldFakerArray An optional array containing old faker values to include in the result.
     *
     * @return array An array containing the formatted faker array and an array of associated models.
     */
    public function getFakerArray(array $oldFakerArray = []): array
    {
        $fakerArrayString = "";
        $models = array();
        foreach ($this->columns as $column) {
            if (!in_array($column->columnName, IgnoreArray::FACTORY_COLUMNS)) {
                $result = $column->getFaker();

                if (!$column->isChanged() && isset($oldFakerArray[$column->columnName])) {
                    $fakerArrayString .= "'$column->columnName' => " . $oldFakerArray[$column->columnName] . "\n";
                } else {
                    $fakerArrayString .= $result['faker'];
                    $model = $result['model'];
                    if ($model != null) {
                        $models[] = $model;
                    }
                }
            }
        }
        return [
            'faker'    => ArrayHelper::formatKeyValuePairs($fakerArrayString),
            'models'   => $models
        ];
    }

    /**
     * Get the model fillable columns of the table as a formatted string.
     *
     * @return string
     */
    public function getModelFillable(): string
    {
        $fillableArrayString = "";

        foreach ($this->columns as $column) {
            if (!in_array($column->columnName, IgnoreArray::MODEL_FILLABLE_COLUMNS)) {
                $fillableArrayString .= "\t\t'" . $column->columnName . "',\n";
            }
        }
        return rtrim($fillableArrayString, "\n\r");
    }

    /**
     * Get the resource array for the table as a formatted string.
     *
     * @param array $oldResource An optional array containing old resource values to include in the result.
     *
     * @return string The formatted resource array string.
     */
    public function getResourceArray(array $oldResource = []): string
    {
        $resourceArrayString = "";

        foreach ($this->columns as $column) {
            if (!in_array($column->columnName, IgnoreArray::RESOURCE_COLUMNS)) {
                if (!$column->isChanged() && isset($oldResource[$column->columnName])) {
                    $resourceValue = $oldResource[$column->columnName] . "\n";
                } else {
                    $resourceValue = "\$this->" . $column->columnName . ",\n";
                }
                $resourceArrayString .= "'" . $column->columnName . "' => " . $resourceValue;
            }
        }

        return ArrayHelper::formatKeyValuePairs($resourceArrayString);
    }


    /**
     * Get the column with the specified column name.
     *
     * @param string $columnName The name of the column to retrieve.
     *
     * @return Column|null The column object if found, or null if not found.
     */
    public function getColumn(string $columnName)
    {
        foreach ($this->columns as $column) {
            if ($column->columnName == $columnName) {
                return $column;
            }
        }
        return null;
    }

    /**
     * Compare the current Table object with another Table object for equality.
     *
     * @param Table $table The Table object to compare.
     *
     * @return bool True if the objects are equal, false otherwise.
     */
    public function equals(Table $table)
    {
        // Check if the number of columns is different
        if (count($this->columns) !== count($table->columns)) {
            return false;
        }

        // Check for inequality in basic properties and relations
        if (
            $this->tableName != $table->tableName ||
            $this->foreignKeysIndex != $table->foreignKeysIndex ||
            $this->extraColumnsCount != $table->extraColumnsCount ||
            $this->isPivot != $table->isPivot ||
            !empty(array_diff($this->relations, $table->relations))
        ) {
            return false;
        }
        // Check equality for each column
        foreach ($this->columns as $column) {
            if (!$this->hasEquivalentColumn($column, $table->columns)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if an equivalent column exists in the given array of columns.
     *
     * @param Column $column The Column object to check.
     * @param Column[] $columnsArray The array of Column objects to search.
     *
     * @return bool True if an equivalent column exists, false otherwise.
     */
    private function hasEquivalentColumn(Column $column, array $columnsArray)
    {
        foreach ($columnsArray as $otherColumn) {
            if ($column->equals($otherColumn)) {
                return true;
            }
        }
        return false;
    }
}
