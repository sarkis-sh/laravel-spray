<?php

declare(strict_types=1);

namespace src\CodeGeneration\Postman\Request\Generic;

use src\CodeGeneration\Postman\Request\RequestBodyGenerator;
use src\CodeGeneration\Postman\Request\RequestGenerator;
use src\Constants\Postman\Method;
use src\Models\Database\Structure\Table;
use src\Models\Postman\Item;
use src\Models\Postman\Request;
use src\Models\Postman\Url;


/**
 * Class PostRequest
 *
 * This class is responsible for generating a Postman request for the POST method.
 * It extends the RequestGenerator class and implements the generate() method.
 * 
 * @package src\CodeGeneration\Postman\Request\Generic
 */
class PostRequest extends RequestGenerator
{
    /**  @var bool Indicates whether the request is for a list of items. */
    private bool $isList;

    /** @var string The body mode for the request. */
    private string $bodyMode;

    /** @var Table The selected table. */
    private Table $selectedTable;


    /**
     * @param Table $selectedTable The selected table. 
     * @param string $apiName The name of the API.
     * @param string $bodyMode The body mode for the request.
     * @param bool $isList Indicates whether the request is for a list of items.
     */
    public function __construct(Table $selectedTable, string $apiName, string $bodyMode, bool $isList = false)
    {
        $this->apiName = $apiName;
        $this->isList = $isList;
        $this->bodyMode = $bodyMode;

        $this->method = Method::POST;

        $this->selectedTable = $selectedTable;

        $modelName = str_replace("_", "-", $selectedTable->tableName);


        $this->pathSegments[] = $modelName;
        if ($isList) {
            $this->pathSegments[] = 'bulk';
        }
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
        $body = (new RequestBodyGenerator($this->selectedTable, $this->bodyMode, $this->isList))
            ->generate();

        $url = new Url($this->pathSegments);

        $request = new Request($this->method, $url, $body);

        $item =  new Item($this->apiName, $request);

        return $item;
    }
}
