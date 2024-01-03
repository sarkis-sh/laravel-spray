<?php

declare(strict_types=1);

namespace src\Models\Http;


/**
 * Class Response
 *
 * Represents an HTTP response.
 * 
 * @package src\Models\Http
 */
class Response
{
    /** @var string The status of the response. */
    public string $status;

    /** @var array|null The body of the response. */
    public ?array $body;

    /** @var int The HTTP status code of the response. */
    public int $code;

    /**
     * Response constructor.
     *
     * @param string $status The status of the response.
     * @param array|null $body The body of the response.
     * @param int $code The HTTP status code of the response.
     */
    public function __construct(string $status, ?array $body, int $code)
    {
        $this->status = $status;
        $this->body = $body;
        $this->code = $code;
    }
}
