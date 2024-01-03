<?php

declare(strict_types=1);

namespace src\CodeGeneration\Postman\Request;

use src\CodeGeneration\AbstractCodeGenerator;
use src\Models\Postman\Query;

/**
 * Class RequestGenerator
 *
 * This abstract class serves as a base for Postman request generators.
 * It implements the CodeGeneratorInterface and provides common properties for request generation.
 * 
 * @package src\CodeGeneration\Postman\Request
 */
abstract class RequestGenerator extends AbstractCodeGenerator
{
    /** @var string The name of the API. */
    protected string $apiName;

    /** @var string The HTTP method for the request. */
    protected string $method;

    /** @var array The path segments for the request URL. */
    protected array $pathSegments;

    /** @var Query[]|null The query params for request. */
    protected ?array $query = null;
}
