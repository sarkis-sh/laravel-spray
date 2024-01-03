<?php

declare(strict_types=1);

namespace src\Helpers;

/**
 * Class StubFileProcessor
 *
 * Provides methods for processing stub files.
 *
 * @package src\Helpers
 */
class StubFileProcessor
{
    /**
     * Retrieves the contents of a stub file and replaces variables with provided values
     *
     * @param string $stubPath The path to the stub file
     * @param array $stubVariables An associative array where keys represent variable names to replace and values are the replacement values
     * @return string The contents of the stub file with variables replaced
     */
    public static function replaceVariables(string $stubPath, array $stubVariables = []): string
    {
        // Read the contents of the stub file
        $stubFileContents = file_get_contents($stubPath);

        // replace the variables
        foreach ($stubVariables as $search => $replace) {
            $stubFileContents = str_replace('{{' . $search . '}}', $replace, $stubFileContents);
        }

        return $stubFileContents;
    }
}
