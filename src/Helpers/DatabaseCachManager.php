<?php

declare(strict_types=1);

namespace src\Helpers;

use src\Services\UserProjectsService;
use src\Models\Database\Structure\Database;
use src\Shared\CodeGenerationContext as CGC;

/**
 * Class DatabaseCachManager
 *
 * Helper class for managing the database cache.
 *
 * @package src\Helpers
 */
class DatabaseCachManager
{
    /**
     * Updates the current database and manages cache.
     *
     * @param Database $currentDatabase The current database instance.
     * @return void
     */
    public static function update(Database $currentDatabase, UserProjectsService $userProjectsService): void
    {
        if (file_exists(CGC::$latestDBVersionPath)) {
            $latestDB = unserialize(file_get_contents(CGC::$latestDBVersionPath));
            if (!$currentDatabase->equals($latestDB)) {
                $userProjectsService->updateTablesStatus($latestDB, $currentDatabase, CGC::$projectName);
                file_put_contents(CGC::$previousDBVersionPath, serialize($latestDB));
                file_put_contents(CGC::$latestDBVersionPath, serialize($currentDatabase));
                return;
            }
        } else {
            file_put_contents(CGC::$latestDBVersionPath, serialize($currentDatabase));
            $userProjectsService->updateTablesStatus(new Database([]), $currentDatabase, CGC::$projectName);
            return;
        }
    }

    /**
     * Retrieves the previous version of the database from cache.
     *
     * @return Database|null The previous database instance, or null if not found.
     */
    public static function getPreviousVersion()
    {
        if (file_exists(CGC::$previousDBVersionPath)) {
            return unserialize(file_get_contents(CGC::$previousDBVersionPath));
        } else {
            return null;
        }
    }
}
