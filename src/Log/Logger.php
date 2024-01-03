<?php

declare(strict_types=1);

namespace src\Log;

use src\Config\Paths\OutPaths;
use src\Shared\CodeGenerationContext as CGC;

/**
 * Class Logger
 *
 * @package src\Log
 */
class Logger
{
    /**
     * Log an informational message.
     *
     * @param string $message The message to log.
     * @return void
     */
    public static function info(string $message): void
    {
        self::log('INFO', $message);
    }

    /**
     * Log an error message.
     *
     * @param string $message The message to log.
     * @return void
     */
    public static function error(string $message): void
    {
        self::log('ERROR', $message);
    }

    /**
     * Log a message at the specified level.
     *
     * @param string $level The log level.
     * @param string $message The message to log.
     * @return void
     */
    private static function log(string $level, string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp][$level] $message\n";

        file_put_contents(CGC::$logDirectory . OutPaths::GENERATION_LOG_FILE, $logEntry, FILE_APPEND);
    }
}
