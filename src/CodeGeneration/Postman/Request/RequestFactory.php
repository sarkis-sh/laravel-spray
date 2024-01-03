<?php

declare(strict_types=1);

namespace src\CodeGeneration\Postman\Request;

use Exception;
use src\CodeGeneration\Postman\Request\Generic\DeleteRequest;
use src\CodeGeneration\Postman\Request\Generic\GetRequest;
use src\CodeGeneration\Postman\Request\Generic\PostRequest;
use src\CodeGeneration\Postman\Request\Generic\PutRequest;
use src\CodeGeneration\Postman\Request\RequestGenerator;
use src\Constants\API;
use src\Models\Database\Structure\Table;

/**
 * Class RequestFactory
 *
 * This class is responsible for creating instances of request generators based on the specified API type.
 * 
 * @package src\CodeGeneration\Postman\Request
 */
class RequestFactory
{
    /**
     * Creates an instance of a specific API type.
     *
     * @param Table $selectedTable The selected table. 
     * @param string $apiType The type of the database to create.
     * @param array $projectProps The project properties.
     * @return RequestGenerator An instance of the specified database type.
     * @throws Exception If the database type is not supported.
     */
    public static function create(Table $selectedTable, string $apiType, array $projectProps): RequestGenerator
    {
        switch ($apiType) {
            case API::GET_ALL['name']:
                return new GetRequest($selectedTable, API::GET_ALL['option_name'], false, true);
            case API::FIND_BY_ID['name']:
                return new GetRequest($selectedTable, API::FIND_BY_ID['option_name'], true);
            case API::STORE['name']:
                return new PostRequest($selectedTable, API::STORE['option_name'], constant("src\Constants\Postman\BodyMode::" . $projectProps["request"]["store"]));
            case API::BULK_STORE['name']:
                return new PostRequest($selectedTable, API::BULK_STORE['option_name'], constant("src\Constants\Postman\BodyMode::" . $projectProps["request"]["bulk_store"]), true);
            case API::UPDATE['name']:
                return new PutRequest($selectedTable, API::UPDATE['option_name']);
            case API::DELETE['name']:
                return new DeleteRequest($selectedTable, API::DELETE['option_name']);
            case API::BULK_DELETE['name']:
                return new DeleteRequest($selectedTable, API::BULK_DELETE['option_name'], true);
            default:
                throw new Exception("Unsupported API: $apiType");
        }
    }
}
