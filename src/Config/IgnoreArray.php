<?php

declare(strict_types=1);

namespace src\Config;

use src\Constants\API;


/**
 * Class IgnoreArray
 *
 * This class defines arrays of elements to ignore in the code generation process.
 * 
 * @package src\Config
 */
class IgnoreArray
{
    /** @var string[] Elements to ignore in the generated FormRequest columns. */
    public const FORM_REQUEST_COLUMNS   = ['id', 'created_at', 'updated_at'];


    /** @var string[] Elements to ignore in the generated Resource columns. */
    public const RESOURCE_COLUMNS   = ['updated_at'];

    /** @var string[] Elements to ignore in the generated model fillable columns. */
    public const MODEL_FILLABLE_COLUMNS = ['id', 'created_at', 'updated_at'];

    /** @var string[] Elements to ignore in the generated factory columns. */
    public const FACTORY_COLUMNS        = ['id', 'created_at', 'updated_at'];

    /**
     * Columns used to count extra columns in the table.
     *
     * These columns are used to verify if a table is a pivot table.
     *
     * @var string[] Elements used to count extra columns in the table.
     */
    public const EXTRA_COLUMNS          = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /** @var string[] Tables to ignore in the database reflection process. */
    public const TABLES                 = ['failed_jobs', 'migrations', 'password_reset_tokens', 'personal_access_tokens'];

    /** @var string[] API methods to ignore in the request validator generation process. */
    public const API                    = [API::GET_ALL, API::FIND_BY_ID, API::DELETE];
}
