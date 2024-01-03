<?php

declare(strict_types=1);

namespace src\Models\Postman;

use JsonSerializable;

/**
 * Represents a Postman request.
 *
 * This class holds information such as the request method, URL, request body, and headers.
 *
 * @package src\Models\Postman
 */
class Request implements JsonSerializable
{
    /** @var string $method The method type (GET, POST, PUT, DELETE). */
    public string $method;

    /** @var Url $url The Url object. */
    public Url $url;

    /** @var Body|null $body The Body object. Default is null. */
    public ?Body $body;

    /** @var Header[] $headerArray An array of Header objects. Default is []. */
    public array $header;


    /**
     * @param string $method The method type (GET, POST, PUT, DELETE).
     * @param Url $url The Url object.
     * @param Body|null $body The Body object. Default is null.
     * @param Header[] $header
     */
    public function __construct(
        string $method,
        Url $url,
        ?Body $body = null,
        array $header = []
    ) {
        $this->method = $method;
        $this->url = $url;
        $this->body = $body;
        $this->header = $header;
    }


    public function JsonSerialize(): array
    {
        $data = [
            "method"    => $this->method,
            "header"    => $this->header,
            "url"       => $this->url
        ];

        if ($this->body !== null) {
            $data["body"] = $this->body;
        }

        return $data;
    }
}
