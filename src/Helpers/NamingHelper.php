<?php

declare(strict_types=1);

namespace src\Helpers;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use src\Constants\NameFormatType;


/**
 * Class NamingHelper
 *
 * This class provides methods for generating class names and variable names based on words, following Laravel conventions.
 * 
 * @uses \Doctrine\Inflector\Inflector
 * @package src\Helpers
 */
class NamingHelper
{
    /**
     * Generates a class name from a word.
     *
     * The input word is first singularized, then classified to produce a properly formatted class name that is singular.
     * 
     * @param string $word The word to transform into a class name.
     *
     * @return string The generated class name.
     */
    public static function getClassName(string $word): string
    {
        if (empty($word)) {
            throw new \InvalidArgumentException('Word is required');
        }

        $singularized = static::inflector()->singularize($word);

        $className = static::inflector()->classify($singularized);

        return $className;
    }

    /**
     * Generates a variable name from a word in the given format.
     *
     * The word is first camelized, then inflected to the appropriate singular or plural form based on the format type.
     *
     * @param string $word The word to transform into a variable name.
     *
     * @param string NameFormatType $formatType The format type, singular or plural.
     *
     * @return string  The generated variable name as a string.
     */
    public static function getVarName(string $word, string $formType): string
    {
        if (empty($word)) {
            throw new \InvalidArgumentException('Word is required');
        }

        $camelized = static::inflector()->camelize($word);

        switch ($formType) {
            case NameFormatType::SINGULAR:
                $varName = static::inflector()->singularize($camelized);
                break;
            case NameFormatType::PLURAL:
                $varName = static::inflector()->pluralize($camelized);
                break;
        }

        return $varName;
    }

    /**
     * Convert a camelCase string to Title Case.
     *
     * @param string $input The camelCase input string.
     *
     * @return string The Title Case converted string.
     */
    public static function camelCaseToTitleCase(string $input): string
    {
        // Insert a space before each capital letter that is not at the beginning of the string
        $words = preg_replace('/(?<!^)([A-Z])/', ' $1', $input);

        return $words;
    }

    /**
     * Convert a snake_case string to Title Case.
     *
     * @param string $input The snake_case input string.
     *
     * @return string The Title Case converted string.
     */
    public static function snakeCaseToTitleCase(string $input): string
    {
        // Split the string into an array of words
        $words = explode('_', $input);

        // Capitalize the first letter of each word and join them with a space
        $output = implode(' ', array_map('ucfirst', $words));

        return $output;
    }

    /**
     * Get the inflector instance.
     *
     * @return \Doctrine\Inflector\Inflector
     */
    public static function inflector(): Inflector
    {
        static $inflector;

        if (is_null($inflector)) {
            $inflector = InflectorFactory::createForLanguage('english')->build();
        }

        return $inflector;
    }
}
