<?php

declare(strict_types=1);

namespace src\Helpers;


use src\Config\Paths\LaravelPaths;

/**
 * Class EnvHelper
 *
 * Provides methods for reading and setting environment variables from .env file.
 *
 * @package src\Helpers
 */
class EnvHelper
{
    /**
     * Reads and sets environment variables from a file.
     *
     * @param string $laravelRootDirectory The Laravel root directory.
     * @return void
     */
    public static function setEnv(string $laravelRootDirectory): void
    {
        $envFilePath = $laravelRootDirectory . LaravelPaths::ENV_FILE_PATH;

        $var_arrs = array();
        // Open the .env file using the reading mode
        $fopen = fopen($envFilePath, 'r');
        if ($fopen) {
            //Loop the lines of the file
            while (($line = fgets($fopen)) !== false) {
                // Check if line is a comment
                $line_is_comment = (substr(trim($line), 0, 1) == '#') ? true : false;
                // If line is a comment or empty, then skip
                if ($line_is_comment || empty(trim($line)))
                    continue;

                // Split the line variable and succeeding comment on line if exists
                $line_no_comment = explode("#", $line, 2)[0];
                // Split the variable name and value
                $env_ex = preg_split('/(\s?)\=(\s?)/', $line_no_comment);
                $env_name = trim($env_ex[0]);
                $env_value = isset($env_ex[1]) ? trim($env_ex[1]) : "";
                $var_arrs[$env_name] = $env_value;
            }
            // Close the file
            fclose($fopen);
        }

        // Set the environment variables
        foreach ($var_arrs as $name => $value) {
            putenv("{$name}={$value}");
        }
    }
}
