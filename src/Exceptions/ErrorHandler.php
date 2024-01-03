<?php

namespace src\Exceptions;

use src\Config\Paths\OutPaths;
use src\Constants\Color;
use src\Shared\CodeGenerationContext as CGC;

/**
 * Class ErrorHandler
 * A class to handle exceptions and errors
 * 
 * @package src\Exceptions
 */
class ErrorHandler
{
    /** @var int The count of errors */
    private int $errorsCount = 0;

    /**
     * Handles an exception by logging the exception message and stack trace
     *
     * @param mixed $exception The exception object
     * @return void
     */
    public function handleException($exception): void
    {
        // Log the exception message
        $this->log('EXCEPTION', 'Exception: ' . $exception->getMessage());

        // Log the stack trace
        $this->log('EXCEPTION', 'Stack Trace: ' . $exception->getTraceAsString());
    }


    /**
     * Handles an error by logging the error or warning message, file, and line number
     *
     * @param int $errorLevel The level of the error
     * @param string $errorMessage The error message
     * @param string $errorFile The file where the error occurred
     * @param int $errorLine The line number where the error occurred
     * @return void
     */
    public function handleError(int $errorLevel, string $errorMessage, string $errorFile, int $errorLine): void
    {
        // Log the error or warning message
        $this->log('ERROR', "Error (Level: $errorLevel): $errorMessage");

        // Log the error file and line number
        $this->log('ERROR', "File: $errorFile, Line: $errorLine");

        // Check if the error is a warning
        if (in_array($errorLevel, [E_WARNING, E_USER_WARNING])) {
            $this->handleWarning($errorLevel, $errorMessage, $errorFile, $errorLine);
        }
    }

    /**
     * Handles a warning by logging the warning message, file, and line number
     *
     * @param int $warningLevel The level of the warning
     * @param string $warningMessage The warning message
     * @param string $warningFile The file where the warning occurred
     * @param int $warningLine The line number where the warning occurred
     * @return void
     */
    public function handleWarning(int $warningLevel, string $warningMessage, string $warningFile, int $warningLine): void
    {
        // Log the warning message
        $this->log('WARNING', "Warning (Level: $warningLevel): $warningMessage");

        // Log the warning file and line number
        $this->log('WARNING', "File: $warningFile, Line: $warningLine");
    }

    /**
     * Logs a message with the specified level and appends it to the log file
     *
     * @param string $level The log level
     * @param string $message The log message
     * @return void
     */
    private function log(string $level, string $message)
    {
        // Get the current date and time
        $timestamp = date('Y-m-d H:i:s');

        // Create the log message with timestamp
        $logEntry = "[$timestamp][$level] $message\n";

        // Append the log message to the log 

        file_put_contents(CGC::$logDirectory . OutPaths::ERROR_LOG_FILE, $logEntry, FILE_APPEND);

        $this->printError();
    }

    /**
     * Prints an error message if it hasn't been printed before
     *
     * @return void
     */
    private function printError()
    {
        if ($this->errorsCount == 0) {
            print("\e[" . Color::RED . "mAn error has occurred. Please review [" . CGC::$logDirectory . "\\spray_errors.log] file.\e[0m\n");
            $this->errorsCount++;
        }
    }
}
