<?php

declare(strict_types=1);

namespace src\CodeGeneration\Postman\Request;

use src\CodeGeneration\AbstractCodeGenerator;
use src\Config\IgnoreArray;
use src\Constants\Postman\RawOptions;
use src\Models\Database\Structure\Table;
use src\Models\Postman\Body;
use src\Models\Postman\Formdata;
use src\Models\Postman\Urlencoded;


/**
 * Class RequestBodyGenerator
 *
 * This class is responsible for generating the request body for Postman requests.
 * It implements the CodeGeneratorInterface and provides methods for generating the body in different modes.
 * @package src\CodeGeneration\Postman\Request
 */
class RequestBodyGenerator extends AbstractCodeGenerator
{
    /** @var Table The selected table for which the request body is generated. */
    private Table $selectedTable;

    /** @var string The body mode for the request. */
    private string $bodyMode;

    /** @var bool Indicates whether the request is for a list of items. */
    private bool $isList;


    /**
     * @param Table $selectedTable The selected table for which the request body is generated.
     * @param string $bodyMode The body mode for the request.
     * @param bool $isList Indicates whether the request is for a list of items.
     */
    public function __construct(Table $selectedTable, string $bodyMode, bool $isList = false)
    {
        $this->selectedTable = $selectedTable;
        $this->bodyMode = $bodyMode;
        $this->isList = $isList;
    }

    /**
     * Generate the request body.
     *
     * This method generates the request body based on the specified body mode.
     *
     * @return Body The generated request body.
     */
    public function generate(array $params = []): Body
    {
        return $this->{$this->bodyMode}();
    }

    /**
     * Generate the request body in formdata mode.
     *
     * This method generates the request body in formdata mode by iterating through the table columns
     * and creating formdata items for each column.
     *
     * @return Body The generated request body in formdata mode.
     */
    private function formdata(): Body
    {
        $data = [];
        foreach ($this->selectedTable->columns as $column) {
            $key = $column->columnName;
            if (!in_array($key, IgnoreArray::FORM_REQUEST_COLUMNS)) {
                $value = $key . ' value';
                $description = $column->getColumnDescription();
                if ($this->isList) {
                    $key = "list[0][$key]";
                }
                $formdata = new Formdata($key, $value, $description);
                $data[] = $formdata;
            }
        }
        return new Body($this->bodyMode, $data);
    }

    /**
     * Generate the request body in raw mode.
     *
     * This method generates the request body in raw mode by iterating through the table columns
     * and creating a raw data array for each column.
     *
     * @return Body The generated request body in raw mode.
     */
    private function raw(): Body
    {
        $temp = [];
        foreach ($this->selectedTable->columns as $column) {
            $key = $column->columnName;
            if (!in_array($key, IgnoreArray::FORM_REQUEST_COLUMNS)) {
                $value = $key . ' value';
                $temp[$key] = $value;
            }
        }
        if ($this->isList) {
            $data['list'] = [$temp];
        } else {
            $data = $temp;
        }
        return new Body($this->bodyMode, null, $data, null, RawOptions::JSON);
    }


    /**
     * Generate the request body in urlencoded mode.
     *
     * This method generates the request body in urlencoded mode by iterating through the table columns
     * and creating urlencoded items for each column.
     *
     * @return Body The generated request body in urlencoded mode.
     */
    private function urlencoded(): Body
    {
        $data = [];
        foreach ($this->selectedTable->columns as $column) {
            $key = $column->columnName;
            if (!in_array($key, IgnoreArray::FORM_REQUEST_COLUMNS)) {
                $value = $key . ' value';
                $description = $column->getColumnDescription();
                if ($this->isList) {
                    $key = "list[0][$key]";
                }
                $urlencoded = new Urlencoded($key, $value, $description);
                $data[] = $urlencoded;
            }
        }
        return new Body($this->bodyMode, null, null, $data);
    }
}
