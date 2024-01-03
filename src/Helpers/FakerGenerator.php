<?php

declare(strict_types=1);

namespace src\Helpers;

use src\Constants\DateFormat;
use src\Constants\Faker;
use src\Constants\NumericSize;
use src\Constants\DataType;
use src\Helpers\NamingHelper;
use src\Models\Database\Structure\Column;


/**
 * Class FakerGenerator
 *
 * Provides methods for generating php faker functions for database columns.
 *
 * @package src\Helpers
 */
class FakerGenerator
{
    /**
     * Generates fake data for a given column.
     *
     * This function considers various scenarios including foreign key references and column types to
     * generate suitable fake data.
     *
     * @param Column $column The column for which fake data is generated.
     *
     * @return array An array containing the generated faker code and, in some cases, the associated model.
     */
    public static function generate(Column $column): array
    {
        $typeFaker = self::getTypeFaker($column);

        /**
         * @var string $model. laravel model name. if the column is foreign key, then the model should be used in factory class 
         */
        $model = null;


        // ForeignKey column faker.
        if ($column->foreignKey != null) {
            $className = NamingHelper::getClassName($column->foreignKey->referencedTable);
            $faker = "$className::all()->pluck('id')->random()";
            $model = $className;
        }

        // Faker by column name
        elseif (isset(Faker::COLUMN_NAME_FAKER[$column->columnName])) {
            $faker = Faker::COLUMN_NAME_FAKER[$column->columnName];
        }

        // Set or enum faker
        elseif (sizeof($column->values) > 0) {
            $values = implode(', ', array_map(function ($value) {
                return "'$value'";
            }, $column->values));

            $faker = "\$this->faker->randomElement([$values])";
        }

        // Faker by type.
        elseif ($typeFaker !== null) {
            $faker = $typeFaker;
        }

        // No available faker
        else {
            $faker = "''";
        }

        return [
            'faker'         => $faker,
            'model'         => $model
        ];
    }


    /**
     * Determines the appropriate Faker method for generating data based on the column's data type.
     *
     * This function checks the column type and returns the corresponding Faker method or `null`
     * if no suitable method is found.
     *
     * @param Column $column The column for which fake data is generated.
     *
     * @return string|null A string representing the Faker method or `null` if no suitable method is found.
     */
    public static function getTypeFaker(Column $column)
    {
        $type = $column->dataType;
        $precision = $column->precision;

        // Set numeric scale '2' if the decimal column scale is null.
        $scale = $column->scale !== null ? $column->scale : 2;

        $charMax = $column->charMaxLength;

        $dateFormat = DateFormat::MAP[$type] ?? null;


        // Calculate the max and min numeric value.
        if ($column->precision !== null && $column->scale !== null) {
            $numMax = pow(10, $precision - $scale) - pow(10, -$scale);

            if ($column->isUnsigned) {
                $numMin = 0;
            } else {
                $numMin = -$numMax;
            }
        } else {
            $numMax = NumericSize::MAP[$type]['max'] ?? 1000;
            if ($column->isUnsigned) {
                $numMin = 0;
            } else {
                $numMin = NumericSize::MAP[$type]['min'] ?? 0;
            }
        }


        $map = [
            DataType::STRING          => "\$this->faker->text($charMax)",
            DataType::TINY_INTEGER    => "\$this->faker->numberBetween($numMin, $numMax)",
            DataType::SMALL_INTEGER   => "\$this->faker->numberBetween($numMin, $numMax)",
            DataType::MEDIUM_INTEGER  => "\$this->faker->numberBetween($numMin, $numMax)",
            DataType::INTEGER         => "\$this->faker->numberBetween($numMin, $numMax)",
            DataType::BIG_INTEGER     => "\$this->faker->numberBetween($numMin, $numMax)",

            DataType::BIT             => ($column->precision != null && $column->precision == 1) ? '$this->faker->boolean' : "\$this->faker->numberBetween(0, " . (pow(2, $precision) - 1) . ")",
            DataType::DECIMAL         => "\$this->faker->randomFloat($scale, $numMin, $numMax)",

            DataType::JSON            => '$this->faker->randomElement([json_encode([\'key\' => \'value\']), json_encode([\'foo\' => \'bar\'])])',

            DataType::DATE_TIME       => "\$this->faker->date('$dateFormat')",
            DataType::DATE            => "\$this->faker->date('$dateFormat')",
            DataType::TIME            => "\$this->faker->date('$dateFormat')",
            DataType::YEAR            => "\$this->faker->date('$dateFormat')",
        ];

        return $map[$column->dataType] ?? null;
    }
}
