<?php

declare(strict_types=1);

namespace src\Utils;

use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use src\Helpers\ArrayHelper;
use src\Helpers\NamingHelper;

/**
 * FileManager class file.
 *
 * This class provides methods for file and directory operations.
 *
 * @package src\Utils
 */
class FileManager
{
    /**
     * Creates a directory at the provided path if it doesn't already exist.
     *
     * @param string $dir The path of the directory to be created.
     * @return bool True if the directory was successfully created, false if the directory already exists.
     * @throws Exception If unable to create the directory.
     */
    public static function makeDirectory(string $dir): bool
    {
        if (!file_exists($dir)) {
            $result = mkdir($dir, 0777, true);
            if (!$result) {
                throw new Exception("Unable to create directory $dir.");
            }
            return true;
        }

        return false;
    }

    /**
     * Writes content to a file at the provided file path, optionally overwriting the file.
     *
     * @param string $filePath The path of the file to be written.
     * @param string $content The content to be written to the file.
     * @param bool $overWrite Optional. Specifies whether to overwrite the file if it already exists. Default is false.
     * @return bool True if the file was successfully written or overwritten, false otherwise.
     */
    public static function write(string $filePath, string $content, bool $overWrite = false): bool
    {
        $fileExists = file_exists($filePath);

        if (!$fileExists || $overWrite) {
            $result = file_put_contents($filePath, $content);
            return ($result !== false);
        }

        return false;
    }

    /**
     * Appends content to a file contents at the provided file content.
     *
     * @param string $fileContents The contents of the file to which the content will be appended.
     * @param string $content The content to be appended to the file.
     * @param string $pattern (optional) A regular expression pattern used to match against the current contents of the file.
     * @param int $mode (optional) A mode flag that controls the behavior of the pattern matching.
     *        If $mode is set to 1, the content will only be appended if the pattern is matched.
     *        If $mode is set to 0, the content will only be appended if the pattern is not matched.
     * @return string The updated file contents after appending the content, or the original contents if no changes were made.
     */
    public static function append(string $fileContents, string $content, string $pattern = "", int $mode = 1): string
    {
        if (!empty($pattern)) {
            if (preg_match($pattern, $fileContents) != $mode) {
                return $fileContents;
            }
        }
        return $fileContents . $content;
    }


    /**
     * Copy a file from a source directory to a destination directory.
     *
     * @param string $sourcePath The path to the file.
     * @param string $destinationPath The path to the destination.
     *
     * @return bool Returns `true` if the file was successfully copied, or `false` if the copy operation failed or if the source file doesn't exist.
     */
    public static function copyFile($sourcePath, $destinationPath)
    {
        self::makeDirectory($destinationPath);

        $destinationFilePath = $destinationPath . '\\' . basename($sourcePath);


        if (file_exists($destinationFilePath)) {
            unlink($destinationFilePath);
        }

        if (file_exists($sourcePath)) {
            if (copy($sourcePath, $destinationFilePath)) {
                return true; // File copy was successful.
            } else {
                return false; // File copy failed.
            }
        } else {
            return false; // Source file doesn't exist.
        }
    }

    /**
     * Recursively copies a folder and its contents from the source path to the destination path.
     *
     * @param string $sourcePath The path of the source folder.
     * @param string $destinationPath The path of the destination folder.
     *
     * @return bool Returns true if all files and subfolders were successfully copied, or false if there was a failure in copying any file or subfolder.
     */
    public static function copyFolder($sourcePath, $destinationPath)
    {
        self::makeDirectory($destinationPath);

        $dir = opendir($sourcePath);
        $copySuccess = true; // Variable to track the success of copying files

        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $sourceFile = $sourcePath . '/' . $file;
                $destinationFile = $destinationPath . '/' . $file;

                if (is_dir($sourceFile)) {
                    // Recursively copy subfolders
                    $subfolderCopySuccess = self::copyFolder($sourceFile, $destinationFile);

                    // If subfolder copy failed, set $copySuccess to false
                    if (!$subfolderCopySuccess) {
                        $copySuccess = false;
                        break; // Return without copying any more files
                    }
                } else {
                    if (file_exists($destinationFile)) {
                        unlink($destinationFile);
                    }
                    if (!copy($sourceFile, $destinationFile)) {
                        $copySuccess = false; // Set $copySuccess to false if copying fails
                        break; // Return without copying any more files
                    }
                }
            }
        }

        closedir($dir);

        return $copySuccess; // Return the overall success status
    }

    /**
     * Find the namespace of class by class name.
     *
     * @param string $projectRoot The project root where the class is exist.
     * @param string $className The class name.
     *
     * @return string|null Returns `class namespace` if the is exist, or `null`if the class file doesn't exist.
     */
    public static function findNamespaceOfClass(string $projectRoot, string $className)
    {
        $classFileName = str_replace('\\', '/', $className) . '.php';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($projectRoot));

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === $classFileName) {
                $fileContents = file_get_contents($file->getPathname());
                $namespacePattern = '/namespace\s+([^\s;]+)/m';
                preg_match($namespacePattern, $fileContents, $matches);

                if (isset($matches[1])) {
                    return $matches[1];
                }
            }
        }

        return null;
    }

    /**
     * Recursively retrieves an array of file names (without extensions) from a directory and its subdirectories.
     *
     * @param string $directory The path to the directory.
     * @param bool $formatted (Optional) Specifies whether to return the file names as a formatted string or an array. Default is false.
     * @param array $ignore (Optional) An array of file names (without extensions) to ignore during the retrieval. Default is an empty array.
     * @param array $oldFileNames An array containing the old file names to merge with.
     * @param string $lang (Optional) The language code. Default is 'en'.
     * @return array|string An array containing file names (without extensions) from the specified directory and its subdirectories. If $formatted is true, a formatted string is returned instead.
     */
    public static function getAllFileNamesInDirectory(string $directory, bool $formatted = false, array $ignore = [], array $oldFileNames = [], string $lang = 'en')
    {
        $fileList = [];
        $fileListString = '';

        // Open a directory, and read its contents
        if (is_dir($directory)) {
            $files = scandir($directory);

            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $path = $directory . DIRECTORY_SEPARATOR . $file;

                    if (is_dir($path)) {
                        // Recursive call for subdirectories

                        if ($formatted) {
                            $fileListString .= self::getAllFileNamesInDirectory($path, $formatted, $ignore, $oldFileNames, $lang);
                        } else {
                            $fileList = array_merge($fileList, self::getAllFileNamesInDirectory($path, $formatted, $ignore, $oldFileNames, $lang));
                        }
                    } else {
                        // Add file name (without extension) to the list
                        $fileName = pathinfo($file, PATHINFO_FILENAME);
                        if (!in_array($fileName, $ignore)) {

                            if ($formatted) {
                                if (isset($oldFileNames[$fileName]) && !empty(trim($oldFileNames[$fileName], '\t\n\r\0\x0B\','))) {
                                    $fileListString .= "'$fileName' => " . $oldFileNames[$fileName] . "\n";
                                } else {
                                    if ($lang == 'en') {
                                        $fileListString .= "'$fileName' => '" . NamingHelper::camelCaseToTitleCase($fileName) . "',\n";
                                    } else {
                                        $fileListString .= "'$fileName' => '',\n";
                                    }
                                }
                            } else {
                                $fileList[] = $fileName;
                            }
                        }
                    }
                }
            }
        }
        if (!empty($fileList))
            return $fileList;
        else
            return ArrayHelper::formatKeyValuePairs($fileListString, "\t");
    }

    /**
     * Deletes a directory and its contents.
     *
     * @param string $dir The path of the directory to delete.
     * @return void
     * @throws \TypeError If $dir is not a string.
     * @throws \RuntimeException If the directory cannot be deleted.
     */
    public static function deleteDirectory(string $dir): void
    {
        if (is_dir($dir)) {

            $files = scandir($dir);

            foreach ($files as $file) {

                if ($file != '.' && $file != '..') {

                    $path = $dir . '/' . $file;

                    if (is_dir($path)) {
                        self::deleteDirectory($path);
                    } else {
                        unlink($path);
                    }
                }
            }

            rmdir($dir);
        }
    }
}
