<?php

declare(strict_types=1);

namespace src\Utils;


/**
 * Uuid class.
 *
 * This class provides a method for generating UUID strings.
 *
 * @package src\Utils
 */
class Uuid
{
    /**
     * Generate a UUID version 4 (UUIDv4) string.
     *
     * This function generates a random UUIDv4 string using the RFC 4122 standard.
     *
     * @return string The generated UUIDv4 string.
     */
    public static function UUID4(): string
    {
        // Generate 16 bytes (128 bits) of random data
        $data = random_bytes(16);

        // Set the version (4) and variant (8, 9, A, or B) bits
        $data[6] = chr(ord($data[6]) & 0x0F | 0x40); // Set version to 4
        $data[8] = chr(ord($data[8]) & 0x3F | 0x80); // Set variant

        // Format the UUID
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));

        return $uuid;
    }
}
