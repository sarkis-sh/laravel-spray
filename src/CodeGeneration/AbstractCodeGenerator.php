<?php

declare(strict_types=1);

namespace src\CodeGeneration;

use src\Log\Logger;


/**
 * Abstract base class for code generators.
 *
 * This class provides a template for code generators and includes a method for
 * generating code and logging the result.
 * 
 * @package src\CodeGeneration
 */
abstract class AbstractCodeGenerator
{
    /**
     * Generate code.
     *
     * This method is responsible for generating code and should be implemented
     * by classes that implement this class.
     * @param array $params An optional array of parameters that can be used during code generation.
     */
    public abstract function generate(array $params = []);

    /**
     * Logs the result of a code generation process.
     *
     * @param string $message The message to log.
     * @param bool|int $result The result of the process.
     * @param string $process The type of process ('created', 'updated'). Default is 'created'.
     *
     * @return void
     */
    protected function log(string $message, $result, string $process = 'created'): void
    {
        if ($result != false) {
            $successMessage = "$message $process successfully";
            Logger::info($successMessage);
        } else {
            $errorMessage = ($process == 'created') ? "$message already exists" : "$message $process failed";
            Logger::error($errorMessage);
        }
    }
}
