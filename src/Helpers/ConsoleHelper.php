<?php

declare(strict_types=1);

namespace src\Helpers;

use src\Constants\Color;


/**
 * Class ConsoleHelper
 *
 * Provides methods for printing text to the console with specified colors.
 *
 * @package src\Helpers
 */
class ConsoleHelper
{
    /** @var string Print stack*/
    public static string $printStack = '';

    /**
     * Adds the given text with the specified color to the print stack.
     *
     * @param string $text  The text to be added to print stack.
     * @param string $color The color to be applied to the text. (Optional, default: Color::WHITE)
     * @return void
     */
    public static function addText(string $text,  string $color = Color::WHITE): void
    {
        $text = "\e[" . $color . "m" . $text . "\e[0m";
        self::$printStack .= $text;
    }

    /**
     * Refreshes the console, prints the current stack, and clears it.
     *
     * @return void
     */
    public static function apply(): void
    {
        self::refreshConsole();
        print(self::$printStack);
        self::$printStack = '';
    }

    /**
     * Refreshes the console by printing a logo and clearing the screen.
     *
     * @return void
     */
    private static function refreshConsole(): void
    {
        $logo = "\e[0;31m" .
            " 
  _____________________________    _____  ____.___.  
 /   _____/\______   \______   \  /  _  \\ \\__ |   |
 \_____  \  |     ___/|       _/ /  /_\  \/   |   |
 /        \ |    |    |    |   \/    |    \____   |
/_______  / |____|    |____|_  /\____|__  / ______|
        \/                   \/         \/\/       
" . "\e[0m\n";

        print(str_repeat("\n", 100));
        print("\033[1;1H");

        self::$printStack = $logo .  self::$printStack;
    }

    /**
     * Read an array of numbers and flags from user input.
     *
     * @return array|bool An array containing the selected numbers and options.
     */
    public static function read()
    {
        $input = readline();

        if ($input === '0') {
            return [
                'numbers'   => [],
                'options'   => [],
                'original'  => '0'
            ];
        }

        // Remove extra spaces
        $string = trim($input);

        // Check if the input is empty
        if (empty($string)) {
            return true;
        }

        // Split the string into an array of numbers and an array of flags
        preg_match_all('/\d+/', $string, $numbers);
        preg_match_all('/-(\w+)/', $string, $flags);

        $numbers = $numbers[0];
        $flags = $flags[0];


        return [
            'numbers'   => $numbers,
            'options'   => $flags,
            'original'  => $string
        ];
    }
}
