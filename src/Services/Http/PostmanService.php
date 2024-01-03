<?php

declare(strict_types=1);

namespace src\Services\Http;

use src\Models\Http\Response;


/**
 * Class PostmanService
 *
 * Provides methods to update and retrieve Postman collections.
 * 
 * @package src\Services\Http
 */
class PostmanService
{

    /**
     * Update a Postman collection.
     *
     * @param mixed $collectionId The ID of the collection to update.
     * @param mixed $apiKey The API key for authentication.
     * @param mixed $collection The updated collection data.
     * @return Response The HTTP response.
     */
    public function updateCollection($collectionId, $apiKey, $collection): Response
    {

        $url = "https://api.getpostman.com/collections/$collectionId";

        $headers = [
            'x-api-key' => $apiKey
        ];

        print("Waiting for update postman collection...\n");

        return (new HttpService())->doPut($url, $collection, $headers);
    }


    /**
     * Get a Postman collection.
     *
     * @param mixed $collectionId The ID of the collection to retrieve.
     * @param mixed $apiKey The API key for authentication.
     * @return Response The HTTP response.
     */
    public function getCollection($collectionId, $apiKey): Response
    {
        $url = "https://api.getpostman.com/collections/$collectionId";

        $headers = [
            'x-api-key' => $apiKey
        ];

        print("Waiting for get latest version of postman collection...\n");

        return (new HttpService())->doGet($url, $headers);
    }
}
