<?php

declare(strict_types=1);

namespace src\Helpers;

use src\Config\Paths\ResourcePaths;
use src\Utils\FileManager;

/**
 * Class ResourceFileManager.
 *
 * This class provides a method to copy resource files specified in the ResourcePaths::RESOURCE_FILES array from a source directory
 * to their corresponding destination directories within the Laravel project. It utilizes the FileManager class for file
 * and folder copying.
 *
 * @package src\Helpers
 */
class ResourceFileManager
{
    /**
     * Copy resource files from a source directory to a destination directory.
     *
     * @param string $laravelRootDirectory The root directory of the Laravel project.
     * 
     * @return bool Returns true if all files were successfully copied, false otherwise.
     */
    public static function copyResourceFiles(string $laravelRootDirectory): bool
    {
        $finalResult = true;
        foreach (ResourcePaths::RESOURCE_FILES as $source => $destination) {
            $destinationFullPath = $laravelRootDirectory . $destination;
            if (is_dir($source)) {
                $result = FileManager::copyFolder($source, $destinationFullPath);
            } else {
                $result = FileManager::copyFile($source, $destinationFullPath);
            }
            if (!$result) {
                $finalResult = false;
            }
        }
        return $finalResult;
    }
}
