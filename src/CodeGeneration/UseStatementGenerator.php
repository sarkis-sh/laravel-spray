<?php

declare(strict_types=1);

namespace src\CodeGeneration;

use src\Helpers\UseStatementAdder;
use src\Shared\CodeGenerationContext as CGC;
use src\Utils\FileManager;

/**
 * Class UseStatementGenerator
 *
 * This class is responsible for generating use statement code.
 *
 * @package src\CodeGeneration
 */
class UseStatementGenerator extends AbstractCodeGenerator
{

    /** @var string The file content */
    private string $fileContents;


    public function __construct(string $fileContents)
    {
        $this->fileContents = $fileContents;
    }

    /**
     * Generate the use statement code.
     * 
     * This method adds use statements for the specified class names in the target file.
     *
     * @param array $params An optional array of parameters that can be used during code generation.
     *                      - 'classNames' (array): An array of class names for which use statements will be generated.
     *                      - 'specificDir' (string): The specific directory to search for the class namespaces.
     *                      - 'useStatement' (string): The specific use statement to be added.
     *
     * @return string The modified content with added use statements.
     */
    public function generate(array $params = []): string
    {
        if (isset($params['classNames'])) {
            foreach ($params['classNames'] as $className) {
                $directory = isset($params['specificDir']) ? CGC::$laravelRootDirectory . $params['specificDir']
                    : CGC::$laravelRootDirectory;

                $namespace = FileManager::findNamespaceOfClass($directory, $className);

                $useStatment =  $namespace != null ? $namespace . "\\" . $className : $params['specificDir'] . "\\" . $className;
                $this->fileContents = UseStatementAdder::add($this->fileContents, $useStatment);
            }
        } else {
            $this->fileContents = UseStatementAdder::add($this->fileContents, $params['useStatement']);
        }

        return $this->fileContents;
    }
}
