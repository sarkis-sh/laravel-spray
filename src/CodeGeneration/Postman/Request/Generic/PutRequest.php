<?php

declare(strict_types=1);

namespace src\CodeGeneration\Postman\Request\Generic;

use src\CodeGeneration\Postman\Request\RequestGenerator;
use src\Config\IgnoreArray;
use src\Constants\Postman\Method;
use src\Constants\NameFormatType;
use src\Helpers\NamingHelper;
use src\Models\Database\Structure\Table;
use src\Models\Postman\Item;
use src\Models\Postman\Query;
use src\Models\Postman\Request;
use src\Models\Postman\Url;


/**
 * Class PutRequest
 *
 * This class is responsible for generating a Postman request for the PUT method.
 * It extends the RequestGenerator class and implements the generate() method.
 * 
 * @package src\CodeGeneration\Postman\Request\Generic
 */
class PutRequest extends RequestGenerator
{
    /** @var Table The selected table. */
    private Table $selectedTable;

    /**
     * @param Table $selectedTable The selected table.
     * @param string $apiName The name of the API.
     * @param string $bodyMode The body mode for the request.
     */
    public function __construct(Table $selectedTable, string $apiName)
    {
        $this->apiName = $apiName;

        $this->method = Method::PUT;

        $this->selectedTable = $selectedTable;

        $modelName = str_replace("_", "-", $selectedTable->tableName);

        $modelId = NamingHelper::getVarName($modelName, NameFormatType::SINGULAR) . "_id";

        $this->pathSegments = [$modelName, "{{{$modelId}}}"];
    }

    /**
     * Generate the Postman request item.
     *
     * This method generates a Postman request item by constructing the URL, request object, and request body.
     *
     * @return Item The generated Postman request item.
     */
    public function generate(array $params = []): Item
    {
        $data = [];
        foreach ($this->selectedTable->columns as $column) {
            $key = $column->columnName;

            if (!in_array($key, IgnoreArray::FORM_REQUEST_COLUMNS)) {
                $value = $key . ' value';
                $description = $column->getColumnDescription();
                $data[] = new Query($key, $value, $description);
            }
        }

        $url = new Url($this->pathSegments, ["{{URL}}"], $data);

        $request = new Request($this->method, $url);

        $item =  new Item($this->apiName, $request);

        return $item;
    }
}
