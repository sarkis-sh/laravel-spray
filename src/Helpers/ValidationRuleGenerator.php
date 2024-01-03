<?php

declare(strict_types=1);

namespace src\Helpers;

use src\Constants\DateFormat;
use src\Constants\DataType;
use src\Models\Database\Structure\Column;


/**
 * Class ValidationRuleGenerator
 *
 * Provides methods for generating Laravel validation rules for a database column.
 *
 * @package src\Helpers
 */
class ValidationRuleGenerator
{
    /**
     * Generates validation rules for a database column.
     *
     * This method creates validation rules based on the characteristics of the column, including its type, nullability,
     * foreign key references, and uniqueness. It can be used for both storing and updating data.
     *
     * @param Column $column The database column for which validation rules are generated.
     * @param bool $forUpdate Indicates whether the rules are for updating data (optional, default is `false`).     *
     * @return string The generated validation rules as a string.
     */
    public static function generate(Column $column, bool $forUpdate = false): string
    {
        $validationArray = [];

        // Check nullabilty.
        if ($column->isNullable) {
            array_push($validationArray, 'nullable');
        } else {
            array_push($validationArray, 'required');
        }

        // Check type validation.
        $typeValidation = self::getTypeValidation($column);

        if ($typeValidation != '') {
            array_push($validationArray, $typeValidation);
        }

        // Check values for enum and set datatype.
        if (sizeof($column->values) > 0) {
            $setValues = implode(',', $column->values);
            array_push($validationArray, "in:$setValues");
        }

        // Check if column is foreign key.
        if ($column->foreignKey != null) {
            $exists = 'exists:' . $column->foreignKey->referencedTable . ',' . $column->foreignKey->referencedColumn;
            array_push($validationArray, $exists);
        }

        // Check unique constraint
        if (in_array($column->key, ['UNI', 'PRI'])) {
            $unique = 'unique:' . $column->tableName . ',' . $column->columnName;
            if ($forUpdate) {
                $unique .= ",' . \$this->id . ',id";
            }
            array_push($validationArray, $unique);
        }

        return implode('|', $validationArray);
    }

    /**
     * Determines the validation rules for a given column type.
     *
     * This function inspects the data type of a column and returns the appropriate Laravel validation rules.
     *
     * @param Column $column.
     *
     * @return string The validation rules as a string, or an empty string if no specific rules apply.
     */
    public static function getTypeValidation(Column $column): string
    {
        $map = [
            DataType::STRING          => ($column->charMaxLength != null) ? 'string|max:' . $column->charMaxLength : 'string',
            DataType::TINY_INTEGER    => 'integer',
            DataType::SMALL_INTEGER   => 'integer',
            DataType::MEDIUM_INTEGER  => 'integer',
            DataType::INTEGER         => 'integer',
            DataType::BIG_INTEGER     => 'integer',
            DataType::BIT             => ($column->precision != null && $column->precision == 1) ? 'boolean' : 'integer',
            DataType::DECIMAL         => 'numeric',
            DataType::JSON            => 'json',
            DataType::DATE_TIME       => 'date|date_format:' . DateFormat::MAP[DataType::DATE_TIME],
            DataType::DATE            => 'date|date_format:' . DateFormat::MAP[DataType::DATE],
            DataType::TIME            => 'date_format:' . DateFormat::MAP[DataType::TIME],
            DataType::YEAR            => 'date_format:' . DateFormat::MAP[DataType::YEAR],
        ];

        return $map[$column->dataType] ?? '';
    }
}
