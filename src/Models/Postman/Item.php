<?php

declare(strict_types=1);

namespace src\Models\Postman;


/**
 * Represents an item in a Postman collection.
 *
 * This class holds information such as the item's name, associated request, and an array of responses.
 *
 * @package src\Models\Postman
 */
class Item
{
    /** @var string $name The name of the Item. */
    public string $name;

    /** @var Request $request The Request object. */
    public Request $request;

    /** @var array $response An array of Response objects. Default is []. */
    public array $response;


    /**
     * @param string $name The name of the Item.
     * @param Request $request The Request object.
     * @param array $response An array of Response objects. Default is [].
     */
    public function __construct(
        string $name,
        Request $request,
        array $response = []
    ) {
        $this->name = $name;
        $this->request = $request;
        $this->response = $response;
    }
}
