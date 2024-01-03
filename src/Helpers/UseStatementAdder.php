<?php

declare(strict_types=1);

namespace src\Helpers;


/**
 * Class UseStatementAdder
 *
 * Provides methods for adding a use statement to a PHP file.
 *
 * @package src\Helpers
 */
class UseStatementAdder
{
    /**
     * Add a use statement to a PHP file contents.
     *
     * @param string $phpFileContents The contents of the PHP file.
     * @param string $useStatement The use statement to append.
     * @return string The modified PHP file contents if the use statement was added, or the original contents if the use statement already exists.
     */
    public static function add(string $phpFileContents, string $usestatement): string
    {
        // Check if the use statement already exists in the file

        if (strpos($phpFileContents, trim($usestatement)) === false) {
            $usestatement = "use $usestatement;\n";
            // Find the position of the first use statement in the file
            $firstUseStatementPos = strpos($phpFileContents, "use ");

            // If a use statement exists, insert the new use statement before it
            if ($firstUseStatementPos !== false) {
                $endOffirstUseStatementPos = strpos($phpFileContents, ';', $firstUseStatementPos);
                return substr_replace($phpFileContents, $usestatement, ($endOffirstUseStatementPos - ($endOffirstUseStatementPos - $firstUseStatementPos)), 0);
            }
            // If no use statement exists, insert the new use statement after the opening PHP tag or namespace
            else {
                $pos = strpos($phpFileContents, 'namespace');

                // If a namespace exists, insert the new use statement before it
                if ($pos !== false) {
                    $pos = strpos($phpFileContents, ';', $pos);
                    if ($pos !== false) {
                        $pos += strlen(';');
                    }
                    $usestatement = "\n\n$usestatement";
                }
                // If no namespace exists, insert the new use statement after the opening PHP tag
                else {
                    $pos = strpos($phpFileContents, '<?php');
                    if ($pos !== false) {
                        $pos += strlen('<?php');
                    }
                    $usestatement = "\n\n$usestatement";
                }

                if ($pos !== false)
                    return substr_replace($phpFileContents, $usestatement, $pos, 0);
            }
        }

        return $phpFileContents;
    }
}
