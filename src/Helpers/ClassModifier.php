<?php

declare(strict_types=1);

namespace src\Helpers;


/**
 * Class ClassModifier
 *
 * Provides methods for modifying PHP class files.
 *
 * @package src\Helpers
 */
class ClassModifier
{
    /**
     * Appends a new function or functions to a class definition in the provided class content.
     *
     * @param string $classContent The content of the class to which the function(s) will be appended.
     * @param string $content The function(s) to be appended to the class content.
     * @return string The updated class content with the function(s) appended.
     */
    public static function appendFunction(string $classContent, string $content): string
    {
        // Find the position of the last closing brace in the class contents
        $closingBracePos = strrpos($classContent, '}');

        // Create the new class contents by inserting the content at the position of the closing brace
        $newClassContents = substr($classContent, 0, $closingBracePos) . $content . substr($classContent, $closingBracePos);

        return $newClassContents;
    }

    /**
     * Checks if a function exists within a given class content.
     *
     * @param string $classContent The content of the class.
     * @param string $functionName The name of the function to search for.
     * @return bool Returns true if the function exists, false otherwise.
     */
    public static function functionIsExist(string $classContent, string $functionName): bool
    {
        // Define a regular expression pattern to identify the function using the provided $functionName
        $functionIdentifierPattern = "/function\s*$functionName\s*\(/";

        // Search for a match of the function using the regular expression pattern within the class file content
        preg_match($functionIdentifierPattern, $classContent, $matches);
        // If no matches are found, the function does not exist, so return false
        if (empty($matches)) {
            return false;
        }
        // If matches are found, the function exists, so return true
        return true;
    }
}
