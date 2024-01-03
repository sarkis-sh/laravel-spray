<?php

namespace src\Helpers;


class LaravelHelper
{
    /**
     * Checks if a given directory is a Laravel directory by verifying the presence of common Laravel files and directories.
     *
     * @param string $directoryPath The path to the directory to be checked.
     *
     * @return bool If the directory is a Laravel directory, returns true. Otherwise, returns false.
     */
    public static function isLaravelDirectory(string $directoryPath): bool
    {
        // Check for common Laravel files and directories
        $laravelFiles = ['artisan', 'composer.json'];
        $laravelDirectories = ['app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes', 'storage', 'tests', 'vendor'];

        // Check for the existence of Laravel files
        foreach ($laravelFiles as $file) {
            if (!file_exists($directoryPath . DIRECTORY_SEPARATOR . $file)) {
                return false;
            }
        }

        // Check for the existence of Laravel directories
        foreach ($laravelDirectories as $dir) {
            if (!is_dir($directoryPath . DIRECTORY_SEPARATOR . $dir)) {
                return false;
            }
        }

        return true;
    }
}
