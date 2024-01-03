<?php

declare(strict_types=1);

namespace src\CodeGeneration\Postman\Request\Generic;

use src\CodeGeneration\Postman\Request\RequestGenerator;
use src\Constants\Postman\BodyMode;
use src\Constants\Postman\Method;
use src\Constants\Postman\RawOptions;
use src\Constants\NameFormatType;
use src\Helpers\NamingHelper;
use src\Models\Database\Structure\Table;
use src\Models\Postman\Body;
use src\Models\Postman\Item;
use src\Models\Postman\Request;
use src\Models\Postman\Url;


/**
 * Class DeleteRequest
 *
 * This class is responsible for generating a Postman request for the DELETE method.
 * It extends the RequestGenerator class and implements the generate() method.
 * 
 * @package src\CodeGeneration\Postman\Request\Generic
 */
class DeleteRequest extends RequestGenerator
{
    /**  @var bool Indicates whether the request is for a list of items. */
    private bool $isList;

    /** 
     * @param Table $selectedTable The selected table. 
     * @param string $apiName The name of the API.
     * @param bool $isList Indicates whether the request is for a list of items.
     */
    public function __construct(Table $selectedTable, string $apiName, bool $isList = false)
    {
        $this->isList = $isList;
        $this->apiName = $apiName;
        $this->method = Method::DELETE;

        $modelName = str_replace("_", "-", $selectedTable->tableName);

        if ($isList) {
            $this->pathSegments = [$modelName];
        } else {
            $modelId = NamingHelper::getVarName($modelName, NameFormatType::SINGULAR) . "_id";

            $this->pathSegments = [$modelName, "{{{$modelId}}}"];
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
        $body = null;

        if ($this->isList) {
            $body = new Body(BodyMode::RAW, null, ["ids" => []], null, RawOptions::JSON);
        }

        $url = new Url($this->pathSegments);

        $request = new Request($this->method, $url, $body);

        $item = new Item($this->apiName, $request);

        return $item;
    }
}
