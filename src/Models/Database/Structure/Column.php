<?php

declare(strict_types=1);

namespace src\Models\Database\Structure;

use Exception;
use src\Constants\DataType;
use src\Helpers\DatabaseCachManager;
use src\Helpers\FakerGenerator;
use src\Helpers\ValidationRuleGenerator;


/**
 * Represents a column in a database table.
 *
 * This class encapsulates information about a database column, including its name, data type, constraints,
 * and associated foreign key relationships.
 *
 * @package src\Models\Database\Structure
 */
class Column
{
    /** @var string $tableName The name of the table the column belongs to. */
    private string $tableName;

    /** @var string $columnName The name of the column. */
    public string $columnName;

    /** @var string $dataType The type of the column. */
    private string $dataType;

    /** @var int|null $charMaxLength The max length of the string column. */
    private ?int $charMaxLength;

    /** @var bool $isNullable Indicates whether the column is nullable. */
    private bool $isNullable;

    /** @var int|null $precision The precision of the numeric column. */
    private ?int $precision;

    /** @var int|null $scale The scale of the numeric column. */
    private ?int $scale;

    /** @var bool $isUnsigned Indicates whether the column is unsigned. */
    private bool $isUnsigned;

    /** @var string|null $key The key type of the column (e.g., primary key, foreign key). */
    private ?string $key;

    /** @var array|null $values The values of the set or enum column. */
    private ?array $values;

    /** @var ForeignKey|null $foreignKey The ForeignKey object representing a foreign key relationship associated with the column. */
    private ?ForeignKey $foreignKey;


    /**
     * @param string $tableName The name of the table the column belongs to.
     * @param string $columnName The name of the column.
     * @param DataType $dataType The type of the column.
     * @param int|null $charMaxLength The max length of the string column. 
     * @param bool $isNullable Indicates whether the column is nullable.
     * @param bool $isUnsigned Indicates whether the column is unsigned.
     * @param int|null $precision The precision of the numeric column.
     * @param int|null $scale The scale of the numeric column.
     * @param string|null $key The key type of the column (e.g., primary key, foreign key). 
     * @param array|null $values The values of the set or enum column.
     * @param ForeignKey|null $foreignKey The ForeignKey object representing a foreign key relationship associated with the column.
     */
    public function __construct(
        string $tableName,
        string $columnName,
        string $dataType,
        ?int $charMaxLength,
        bool $isNullable,
        bool $isUnsigned,
        ?int $precision,
        ?int $scale,
        ?string $key,
        ?array $values,
        ?ForeignKey $foreignKey
    ) {
        $this->tableName = $tableName;
        $this->columnName = $columnName;
        $this->dataType = $dataType;
        $this->charMaxLength = $charMaxLength;
        $this->isNullable = $isNullable;
        $this->isUnsigned = $isUnsigned;
        $this->precision = $precision;
        $this->scale = $scale;
        $this->key = $key;
        $this->values = $values;
        $this->foreignKey = $foreignKey;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new Exception("Property $name does not exist.");
    }

    /**
     * Get the validation rules for creating a record with this column as a formatted string.
     * 
     * @return string
     */
    public function getStoreValidationRules(): string
    {
        $validationArrayString = ValidationRuleGenerator::generate($this);
        return "'" . $this->columnName . "' => '$validationArrayString',\n";
    }

    /**
     * Get the validation rules for creating a records with this column in array as a formatted string.
     * 
     * @return string
     */
    public function getBulkStoreValidationRules(): string
    {
        $validationArrayString = ValidationRuleGenerator::generate($this);
        return "'list.*." . $this->columnName . "' => '$validationArrayString',\n";
    }

    /**
     * Get the validation rules for updating a record with this column as a formatted string.
     * 
     * @return string 
     */
    public function getUpdateValidationRules(): string
    {
        $validationArrayString = ValidationRuleGenerator::generate($this, true);
        return "'" . $this->columnName . "' => '$validationArrayString',\n";
    }

    /**
     * Get the faker for the column
     * 
     * @return array e.g   return [
     *                          'faker'    => "'$column->columnName'  => $faker,\n",
     *                          'model'    => Model name|null
     *                      ];
     */
    public function getFaker(): array
    {
        $fakerResult = FakerGenerator::generate($this);

        $fakerResult['faker'] = "'$this->columnName'  => " . $fakerResult['faker'] . ",\n";

        return $fakerResult;
    }

    /**
     * Get column description used in generate postman collection
     * 
     * @return string
     */
    public function getColumnDescription(): string
    {
        $description = '';

        if ($this->isNullable) {
            $description .= 'nullable';
        } else {
            $description .= 'required';
        }

        $typeValidation = ValidationRuleGenerator::getTypeValidation($this);

        if ($typeValidation != '') {
            $description .= '|' . $typeValidation;
        }

        return $description;
    }

    /**
     * Check if the column has changed compared to the previous database version.
     *
     * @return bool True if the column has changed, false otherwise.
     */
    public function isChanged(): bool
    {
        /** @var Database $previousDBVersion */
        $previousDBVersion = DatabaseCachManager::getPreviousVersion();

        // If there is no previous database version, consider the column unchanged.
        if ($previousDBVersion == null) {
            return false;
        }

        $table = $previousDBVersion->getTable($this->tableName);

        if ($table == null) {
            return false;
        }

        $column = $table->getColumn($this->columnName);
        if($column == null){
            return false;
        }

        // Compare the current column with the corresponding column in the previous database version.
        return !$this->equals($column);
    }

    /**
     * Compare the current Column object with another Column object for equality.
     *
     * @param Column $column The Column object to compare.
     *
     * @return bool True if the objects are equal, false otherwise.
     */
    public function equals(Column $column)
    {
        return (
            $this->tableName == $column->tableName &&
            $this->columnName == $column->columnName &&
            $this->dataType == $column->dataType &&
            $this->charMaxLength == $column->charMaxLength &&
            $this->isNullable == $column->isNullable &&
            $this->precision == $column->precision &&
            $this->scale == $column->scale &&
            $this->isUnsigned == $column->isUnsigned &&
            $this->key == $column->key &&
            $this->foreignKey == $column->foreignKey &&
            empty(array_diff($this->values, $column->values))
        );
    }
}
