<?php

declare(strict_types=1);

namespace src\Constants;

/**
 * Class API
 *
 * This class contains constants for supported API actions.
 * 
 * @package src\Constants
 */
class API
{
    /** @var string The GET_ALL API action. */
    const GET_ALL         = [
        'name'        => 'getAll',
        'option_name' => 'Get all',
        'option_abr'  => '-ga',
    ];

    /** @var string The FIND_BY_ID API action. */
    const FIND_BY_ID         = [
        'name'        => 'findById',
        'option_name' => 'Find by id',
        'option_abr'  => '-fbi',
    ];

    /** @var string The STORE API action. */
    const STORE         = [
        'name'        => 'store',
        'option_name' => 'Store',
        'option_abr'  => '-s',
    ];

    /** @var string The BULK_STORE API action. */
    const BULK_STORE         = [
        'name'        => 'bulkStore',
        'option_name' => 'Bulk store',
        'option_abr'  => '-bs',
    ];

    /** @var string The UPDATE API action. */
    const UPDATE         = [
        'name'        => 'update',
        'option_name' => 'Update',
        'option_abr'  => '-u',
    ];

    /** @var string The DELETE API action. */
    const DELETE         = [
        'name'        => 'delete',
        'option_name' => 'Delete',
        'option_abr'  => '-d',
    ];

    /** @var string The UPDATE API action. */
    const BULK_DELETE    = [
        'name'        => 'bulkDelete',
        'option_name' => 'Bulk delete',
        'option_abr'  => '-bd',
    ];
}
