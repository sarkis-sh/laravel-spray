<?php

declare(strict_types=1);

namespace src\Database\Drivers;

use mysqli;
use src\Config\DBConfig;
use src\Config\IgnoreArray;
use src\Database\AbstractReflector;
use src\Constants\DataType;
use src\Models\Database\Structure\Column;
use src\Models\Database\Structure\Database;
use src\Models\Database\Structure\ForeignKey;
use src\Models\Database\Structure\Table;


/**
 * Class MySQLReflector
 *
 * This class represents a MySQL database reflector and extends the AbstractReflector class.
 * 
 * @package src\Database\Drivers
 */
class MySQLReflector extends AbstractReflector
{

    /** @var mysqli The MySQL database connection. */
    private mysqli $connection;


    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }


    public function reflect(): Database
    {
        $results = $this->getDatabaseInfo();

        $tables = [];

        foreach ($results as $tableName => $tableColumns) {
            $columns = [];
            $extraColumnsCount = 0;
            $foreignKeysIndex = [];

            foreach ($tableColumns as $tableColumn) {
                $columnName         = $tableColumn['column_name'];
                $dataType           = $this->getReflectedDataType($tableColumn['data_type']);
                $charMaxLenght      = $tableColumn['character_maximum_length'] != null ? intval($tableColumn['character_maximum_length']) : null;
                $columnKey          = $tableColumn['column_key'];
                $isNullable         = (strtolower($tableColumn['is_nullable']) === 'yes');
                $isUnsigned         = ($tableColumn['is_unsigned'] === 'yes');

                $numericPrecision   = !in_array($tableColumn['numeric_precision'], [null, 0, '0']) ?
                    intval($tableColumn['numeric_precision']) : null;

                $numericScale       = !in_array($tableColumn['numeric_scale'], [null, 0, '0']) ?
                    intval($tableColumn['numeric_scale']) : null;

                $referencedTable    = $tableColumn['referenced_table_name'];
                $referencedColumn   = $tableColumn['referenced_column_name'];

                $columnValues = [];
                if ($tableColumn['data_type'] == 'set' || $tableColumn['data_type'] == 'enum') {
                    $columnType = $tableColumn['column_type'];

                    if (preg_match('/^' . $tableColumn['data_type'] . '\((.+)\)$/', $columnType, $matches)) {
                        $enumValues = $matches[1];
                        $enumValues = str_replace("'", "", $enumValues);
                        $enumValues = explode(',', $enumValues);
                        $columnValues  = array_map('trim', $enumValues);
                    }
                    $charMaxLenght = null;
                }

                if (in_array($columnName, IgnoreArray::EXTRA_COLUMNS)) {
                    $extraColumnsCount++;
                }

                $foreignKey = null;
                if ($referencedTable != null && $referencedColumn != null) {
                    $foreignKey = new ForeignKey($referencedTable, $referencedColumn);
                }

                $newColumn = new Column(
                    $tableName,
                    $columnName,
                    $dataType,
                    $charMaxLenght,
                    $isNullable,
                    $isUnsigned,
                    $numericPrecision,
                    $numericScale,
                    $columnKey,
                    $columnValues,
                    $foreignKey
                );

                $columns[] = $newColumn;

                if ($foreignKey != null) {
                    $foreignKeysIndex = array_merge($foreignKeysIndex, [$columnName => $newColumn]);
                }
            }

            $newTable = new Table($tableName, $columns, $foreignKeysIndex, $extraColumnsCount);
            $tables = array_merge($tables, [$tableName => $newTable]);
        }
        return new Database($tables);
    }

    public function getDatabaseInfo(): array
    {
        $tableNames = implode(',', array_map(function ($tableName) {
            return "'$tableName'";
        }, IgnoreArray::TABLES));

        $query = "SELECT DISTINCT
                    c.table_name as table_name,
                    c.column_name as column_name,
                    c.data_type as data_type,
                    c.column_type as column_type,
                    c.character_maximum_length as character_maximum_length,
                    c.numeric_precision as numeric_precision,
                    c.numeric_scale as numeric_scale,
                    c.column_key as column_key,
                    c.is_nullable as is_nullable,
                    kcu.referenced_table_name as referenced_table_name,
                    kcu.referenced_column_name as referenced_column_name,
                    CASE
                        WHEN c.column_type LIKE '%unsigned%'
                        THEN 'yes'
                        ELSE 'no'
                    END AS is_unsigned
                FROM information_schema.columns AS c
                LEFT JOIN information_schema.key_column_usage AS kcu
                    ON c.table_name = kcu.table_name
                    AND c.column_name = kcu.column_name
                WHERE c.table_schema = ?
                AND c.table_name NOT IN ($tableNames)
                ORDER BY c.ordinal_position";

        $statement = $this->connection->prepare($query);
        $tableSchema = DBConfig::$dbDatabase;

        $statement->bind_param('s', $tableSchema);

        $statement->execute();
        $result = $statement->get_result();

        // Check for errors
        if (!$result) {
            die("Query failed: " . $this->connection->error);
        }

        // Initialize an array to store the grouped results
        $groupedResults = [];

        // Process the results and group by table_name
        while ($row = $result->fetch_assoc()) {
            $tableName = $row['table_name'];
            if (!isset($groupedResults[$tableName])) {
                $groupedResults[$tableName] = [];
            }
            $groupedResults[$tableName][] = $row;
        }
        return $groupedResults;
    }

    public function getReflectedDataType(string $dataType): string
    {
        $dataTypeMap = [
            'varchar'    => DataType::STRING,
            'longtext'   => DataType::STRING,
            'text'       => DataType::STRING,
            'mediumtext' => DataType::STRING,
            'char'       => DataType::STRING,
            'tinytext'   => DataType::STRING,
            'binary'     => DataType::STRING,
            'varbinary'  => DataType::STRING,
            'blob'       => DataType::STRING,
            'mediumblob' => DataType::STRING,
            'longblob'   => DataType::STRING,
            'tinyblob'   => DataType::STRING,
            'json'       => DataType::JSON,

            'datetime'   => DataType::DATE_TIME,
            'timestamp'  => DataType::DATE_TIME,
            'date'       => DataType::DATE,
            'time'       => DataType::TIME,
            'year'       => DataType::YEAR,

            'int'        => DataType::INTEGER,
            'bigint'     => DataType::BIG_INTEGER,
            'tinyint'    => DataType::TINY_INTEGER,
            'smallint'   => DataType::SMALL_INTEGER,
            'mediumint'  => DataType::MEDIUM_INTEGER,
            'bit'        => DataType::BIT,
            'float'      => DataType::DECIMAL,
            'decimal'    => DataType::DECIMAL,
            'double'     => DataType::DECIMAL
        ];

        return  $dataTypeMap[$dataType] ?? DataType::NONE;
    }
}
