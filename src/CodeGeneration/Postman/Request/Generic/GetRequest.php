<?php

declare(strict_types=1);

namespace src\CodeGeneration\Postman\Request\Generic;

use src\CodeGeneration\Postman\Request\RequestGenerator;
use src\Constants\Postman\Method;
use src\Constants\NameFormatType;
use src\Helpers\NamingHelper;
use src\Models\Database\Structure\Table;
use src\Models\Postman\Item;
use src\Models\Postman\Query;
use src\Models\Postman\Request;
use src\Models\Postman\Url;


/**
 * Class GetRequest
 *
 * This class is responsible for generating a Postman request for the GET method.
 * It extends the RequestGenerator class and implements the generate() method.
 * 
 * @package src\CodeGeneration\Postman\Request\Generic
 */
class GetRequest extends RequestGenerator
{

    /**
     * @param Table $selectedTable The selected table.
     * @param string $apiName The name of the API.
     * @param bool $byId Indicates whether the request is for a specific item by ID.
     * @param bool $withPagination Indicates whether the request is with pagination.
     */
    public function __construct(Table $selectedTable, string $apiName, bool $byId = false, bool $withPagination = false)
    {
        $this->apiName = $apiName;
        $this->method = Method::GET;

        $modelName = str_replace("_", "-", $selectedTable->tableName);

        if ($byId) {
            $modelId = NamingHelper::getVarName($modelName, NameFormatType::SINGULAR) . "_id";
            $this->pathSegments = [$modelName, "{{{$modelId}}}"];
        } else {
            $this->pathSegments = [$modelName];
        }

        if ($withPagination) {
            $this->query = [
                new Query('limit', '1', 'Max page size'),
                new Query('page', '1', 'Current page')
            ];
        }
    }
    /**
     * Generate the Postman request item.
     *
     * This method generates a Postman request item by constructing the URL and request object.
     *
     * @return Item The generated Postman request item.
     */
    public function generate(array $params = []): Item
    {
        $url = new Url($this->pathSegments, ["{{URL}}"],  $this->query);

        $request = new Request($this->method, $url);

        $item =  new Item($this->apiName, $request);

        return $item;
    }
}
