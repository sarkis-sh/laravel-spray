<?php

declare(strict_types=1);

namespace src\Constants;


/**
 * Class Route
 *
 * This class contains static routes definitions for supported methods.
 * 
 * @package src\Constants
 */
class Route
{
    /** @var array The mapping of APIs to their route definitions. */
    const MAP = [
        API::STORE['name']         => "    Route::post('/', 'store');",
        API::GET_ALL['name']       => "    Route::get('/', 'getAll');",
        API::BULK_STORE['name']    => "    Route::post('/bulk', 'bulkStore');",
        API::FIND_BY_ID['name']    => "    Route::get('/{id}', 'findById');",
        API::UPDATE['name']        => "    Route::put('/{id}', 'update');",
        API::DELETE['name']        => "    Route::delete('/{id}', 'delete');",
        API::BULK_DELETE['name']   => "    Route::delete('/', 'bulkDelete');"
    ];
}
