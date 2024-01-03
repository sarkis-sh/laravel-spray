<?php

declare(strict_types=1);

namespace src\Models\Postman;

use src\Constants\Postman\FormdataType;


/**
 * Represents a row in the form data of a Postman request.
 *
 * This class holds information about a form data row, including the key, value, description, and type.
 *
 * @package src\Models\Postman
 */
class Formdata
{
    /** @var string $key The key of the row. */
    public string $key;

    /** @var string $value The value of the row. */
    public string $value;

    /** @var string $description The description of the row. */
    public string $description;

    /** @var string $type The type of the row. Default is text. */
    public string $type;


    /**
     * @param string $key The key of the row.
     * @param string $value The value of the row.
     * @param string $description The description of the row.
     * @param string $type The type of the row. Default is text.
     */
    public function __construct(
        string $key,
        string $value,
        string $description,
        string $type = FormdataType::TEXT
    ) {
        $this->key = $key;
        $this->value = $value;
        $this->description = $description;
        $this->type = $type;
    }
}
