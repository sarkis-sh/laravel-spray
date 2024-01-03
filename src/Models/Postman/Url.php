<?php

declare(strict_types=1);

namespace src\Models\Postman;

use JsonSerializable;


/**
 * Represents a Postman URL.
 *
 * This class holds information about the URL, including path segments, host segments, and query parameters.
 *
 * @package src\Models\Postman
 */
class Url implements JsonSerializable
{
    /** @var array $path An array representing the path segments. */
    public array $path;

    /** @var array $host An optional array representing the host segments. Default is ["{{URL}}"]. */
    public array $host;

    /** @var string $row The full path of the API. */
    public string $raw;

    /** @var Query[]|null $query array of Query objects */
    public ?array $query;


    /**
     * @param array $path An array representing the path segments.
     * @param array $host An optional array representing the host segments. Default is ["{{URL}}"].
     * @param Query[]|null $query array of Query objects
     */
    public function __construct(
        array $pathSegments,
        array $hostSegments = ["{{URL}}"],
        ?array $query = null
    ) {
        $this->path = $pathSegments;
        $this->host = $hostSegments;
        $mergedPath = array_merge($hostSegments, $pathSegments);
        $this->raw  = implode("/", $mergedPath);

        if ($query != null) {
            foreach ($query as $param) {
                $this->raw .= "?$$param->key=$param->value";
            }
        }

        $this->query = $query;
    }


    public function JsonSerialize(): array
    {
        $data = [
            "raw"   => $this->raw,
            "path"  => $this->path,
            "host"  => $this->host
        ];

        if ($this->query != null) {
            $data["query"] = $this->query;
        }

        return $data;
    }
}
