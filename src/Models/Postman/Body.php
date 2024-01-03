<?php

declare(strict_types=1);

namespace src\Models\Postman;

use JsonSerializable;
use src\Constants\Postman\BodyMode;


/**
 * Represents the body of a Postman request.
 *
 * This class provides a structured representation of the body of a Postman request, including its mode (e.g., row, formdata, urlencoded),
 * and optional data arrays for different modes.
 *
 * @package src\Models\Postman
 */
class Body implements JsonSerializable
{
    /** @var string $mode The mode of the body(e.g., row, formdata, urlencoded). */
    public string $mode;

    /** @var Formdata[]|null $formdata An optional array of Formdata objects. Default is null. */
    public ?array $formdata;

    /** @var array|null $row An optional array of Row objects. Default is null. */
    public ?array $row;

    /** @var array|null $options An optional array representing additional options(e.g., row type:json, text, XML, HTML..) . Default is null. */
    public ?string $options;

    /** @var Urlencoded[]|null $urlencoded An optional array of Urlencoded objects. Default is null. */
    public ?array $urlencoded;


    /** 
     * @param string $mode The mode of the body(e.g., row, formdata, urlencoded).
     * @param Formdata[]|null $formdata An optional array of Formdata objects. Default is null.
     * @param array|null $row An optional array of Row objects. Default is null.
     * @param Urlencoded[]|null $urlencoded
     * @param array|null $options An optional array representing additional options(e.g., row type:json, text, XML, HTML..) . Default is null.
     */
    public function __construct(
        string $mode,
        ?array $formdata = null,
        ?array $row = null,
        ?array $urlencoded = null,
        ?string $options = null
    ) {
        $this->mode = $mode;
        $this->formdata = $formdata;
        $this->row = $row;
        $this->urlencoded = $urlencoded;
        $this->options = $options;
    }


    public function jsonSerialize(): array
    {
        $data = [
            "mode" => $this->mode,
            "$this->mode" => $this->mode == BodyMode::RAW ?
                json_encode($this->row, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) :
                $this->{$this->mode}
        ];

        if ($this->options !== null) {
            $data["options"]["raw"] = ["language" => $this->options];
        }

        return $data;
    }
}
