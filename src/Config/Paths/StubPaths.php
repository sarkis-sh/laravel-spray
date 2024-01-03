<?php

declare(strict_types=1);

namespace src\Config\Paths;


/**
 * Class Path
 *
 * This class contains the paths for stub files.
 * 
 * @package src\Config\Paths
 */
class StubPaths
{
    /** @var string The file path for the validation function stub. */
    const VALIDATION_FUNCTION_STUB_PATH = __DIR__ . '\\..\\..\\..\\stubs\\function.validator.stub';

    /** @var string The file path for the controller stub. */
    const CONTROLLER_STUB_PATH = __DIR__ . '\\..\\..\\..\\stubs\\controller.stub';

    /** @var string The file path for the factory stub. */
    const FACTORY_STUB_PATH = __DIR__ . '\\..\\..\\..\\stubs\\factory.stub';

    /** @var string The file path for the model stub. */
    const MODEL_STUB_PATH = __DIR__ . '\\..\\..\\..\\stubs\\model.stub';

    /** @var string The file path for the request stub. */
    const REQUEST_STUB_PATH = __DIR__ . '\\..\\..\\..\\stubs\\request.stub';

    /** @var string The file path for the resource stub. */
    const RESOURCE_STUB_PATH = __DIR__ . '\\..\\..\\..\\stubs\\resource.stub';

    /** @var string The file path for the service stub. */
    const SERVICE_STUB_PATH = __DIR__ . '\\..\\..\\..\\stubs\\service.stub';

    /** @var string The file path for the routes group stub. */
    const ROUTES_GROUP_STUB_PATH = __DIR__ . '\\..\\..\\..\\stubs\\routes.group.stub';

    /** @var string The file path for the belongsTo stub. */
    const BELONGS_TO_STUB_PATH = __DIR__ . '\\..\\..\\..\\stubs\\relations\\belongsTo.stub';

    /** @var string The file path for the hasMany stub. */
    const HAS_MANY_STUB_PATH = __DIR__ . '\\..\\..\\..\\stubs\\relations\\hasMany.stub';
}
