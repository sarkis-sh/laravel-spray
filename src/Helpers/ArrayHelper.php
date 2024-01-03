<?php

declare(strict_types=1);

namespace src\Helpers;

/**
 * Helper class for working with arrays and array-like structures.
 *
 * This class provides methods for formatting key-value pairs in an array-like string and replacing an old array pattern
 * with a new array in the content of a file.
 *
 * @package src\Helpers
 */
class ArrayHelper
{
    /**
     * Formats an input string as aligned key-value pairs.
     *
     * @param string $keyValuePairsString The keyValuePairsString string containing key-value pairs.
     * @param string $leftMargin The indentation that should be on the left of the array elements. Default is "\t\t\t".
     * @return string The formatted string with aligned key-value pairs.
     */
    public static function formatKeyValuePairs(string $keyValuePairsString, string $leftMargin = "\t\t\t"): string
    {
        $result = self::parseArrayString($keyValuePairsString);
        // Initialize the maximum key length to 0
        $maxKeyLength = 0;

        // Find the maximum key length
        foreach ($result as $key => $value) {
            $maxKeyLength = max($maxKeyLength, strlen($key));
        }
        $maxKeyLength += 2;
        // Format and align the key-value pairs
        $formattedPairs = [];
        foreach ($result as $key => $value) {
            if (!empty($key) && !empty($value)) {
                $key = trim($key);
                $value = trim($value);
                $formattedPairs[] = $leftMargin . sprintf("%-{$maxKeyLength}s  =>  %s", "'$key'", $value);
            }
        }

        // Combine the formatted pairs into a single string with line breaks
        $formattedString = implode("\n", $formattedPairs);

        return $formattedString;
    }

    /**
     * Replaces an old array pattern with a new array in the content of a file.
     *
     * @param string $fileContent The file content to be modified.
     * @param string $newArray The new array content to replace the old array pattern.
     * @param string $oldArrayPattern The regular expression pattern that represents the old array structure.
     * @return string The updated file content, or the original file content if the replacement failed.
     */
    public static function replaceArray(string $fileContent, string $newArray, string $oldArrayPattern): string
    {
        // Search for the old array pattern using a regular expression.
        if (preg_match($oldArrayPattern, $fileContent, $matches, PREG_OFFSET_CAPTURE)) {
            $arrayContents = $matches[1][0];

            //Check if there any comments
            $pattern = '/\/\*(.*?)\*\//s';
            preg_match_all($pattern, $arrayContents, $matchesc);
            if (isset($matchesc[0][0]))
                $newArray = "\n" . $matchesc[0][0] . $newArray;


            if (trim($arrayContents, " \t\n\r\0\x0B[]") != '') {
                $arrayContents = trim($arrayContents, "[]");
            } else {
                $newArray = "[$newArray]";
            }

            $replacement = str_replace($arrayContents, $newArray, $matches[0][0]);
            $fileContent = str_replace($matches[0][0], $replacement, $fileContent);
        }

        return $fileContent;
    }

    /**
     * Parses a key-value array string from a file content or a given string using a provided pattern.
     *
     * @param string $content The file content or array string to be parsed.
     * @param string|null $pattern The regular expression pattern to search for the array. If not provided, the entire content will be processed as the array string.
     * @param bool $isTrimmed Determines if the array contents should be trimmed of surrounding brackets. Defaults to true.
     * @return array The parsed array.
     */
    public static function parseArrayString(string $content, ?string $pattern = null, $isTrimed = true): array
    {
        if ($pattern != null) {
            // Search for the array pattern using a regular expression.
            if (preg_match($pattern, $content, $matches)) {
                $arrayContents = $matches[1];
                if (!$isTrimed) {
                    $arrayContents = trim($arrayContents, "[]");
                }
            }

            if (!isset($arrayContents)) {
                return [];
            }
        } else {
            $arrayContents = $content;
        }


        $level = 0;
        // Initialize an empty output string
        $output = "";
        // Split the input string by the symbol
        $parts = explode("=>", $arrayContents);
        // Loop through the parts
        for ($i = 0; $i < count($parts); $i++) {
            // Append the current part to the output
            $output .= $parts[$i];
            // If this is not the last part, append the replacement with the level

            // Check if the current part contains an opening bracket [
            if (strpos($parts[$i], "[") !== false) {
                // Increase the nesting level by one
                $level++;
            }
            // Check if the current part contains a closing bracket ]
            if (strpos($parts[$i], "]") !== false) {
                // Decrease the nesting level by one
                $level--;
            }
            if ($i < count($parts) - 1) {
                if ($level == 0)
                    $output = preg_replace("/(')([^']*)(')$/", "{{@}}$1$2$3", trim($output));
                $output .= ($level == 0 ? "{{@}}" : "=>");
            }
        }

        $explodedArrayContent = explode("{{@}}", $output);

        $arrayOfParts = array_values(array_filter($explodedArrayContent, function ($item) {
            return !empty(trim($item));
        }));

        $result = [];
        for ($i = 0; $i < sizeof($arrayOfParts); $i += 2) {
            if ($i < sizeof($arrayOfParts) && $i + 1 < sizeof($arrayOfParts)) {
                $value = $arrayOfParts[$i + 1];
                $result[trim($arrayOfParts[$i], "\"'")] = substr(trim($value), -1) == "," ? trim($value) : trim($value) . ",";
            }
        }

        return $result;
    }
}
