<?php

declare(strict_types=1);

namespace src\CodeGeneration;


use src\Config\Paths\LaravelPaths;
use src\Config\Paths\StubPaths;
use src\Utils\FileManager;
use src\Helpers\ArrayHelper;
use src\Helpers\NamingHelper;
use src\Helpers\StubFileProcessor;
use src\Shared\CodeGenerationContext as CGC;

/**
 * Class ResourceGenerator
 *
 * This class is responsible for generating resource code.
 *
 * @package src\CodeGeneration
 */
class ResourceGenerator extends AbstractCodeGenerator
{
    /** @var array Variables used in the stub file for generating the resource code. */
    private array $stubVariables;


    public function __construct()
    {

        $this->stubVariables = [
            'resourceNamespace' => LaravelPaths::RESOURCE_BASE_DIRECTORY
        ];
    }

    /**
     * Generate the resource code.
     * 
     * This method generates a resource file with an array filled with table columns.
     * The generated code is written to the specified resource file path.
     * Optionally, it can update an existing resource file by filling the array with the latest table columns.
     *
     * @return void
     */
    public function generate(array $params = []): void
    {
        foreach (CGC::$selectedTables as $selectedTable) {
            $className = NamingHelper::getClassName($selectedTable->tableName);

            $resourceFilePath = CGC::$laravelRootDirectory . LaravelPaths::RESOURCE_BASE_DIRECTORY . '\\' . $className . 'Resource.php';

            $this->stubVariables = array_merge($this->stubVariables, [
                'className'         => $className,
                'columns'           => $selectedTable->getResourceArray()
            ]);

            if (!file_exists($resourceFilePath)) {
                // Replace resource stub variables and retrieve the final contents of the resource stub file.
                $content = StubFileProcessor::replaceVariables(
                    StubPaths::RESOURCE_STUB_PATH,
                    $this->stubVariables
                );

                // Create the directory path for the resource.
                FileManager::makeDirectory(dirname($resourceFilePath));

                // Write the generated code to the resource file.
                $result = FileManager::write($resourceFilePath, $content);

                // Log the result of the generation process.
                parent::log("Resource [$resourceFilePath]", $result);
            } else {
                $resourceContent = file_get_contents($resourceFilePath);

                $pattern = "/(?:private|public|protected)\\s+function\\s+toArray\\s*\\(.*?\\)\\s*\\{.*?return\\s*(\\[(?:[^\\[\\]]++|(?1))*+\\])\\s*;\\s*\\}/s";
                $oldResource = ArrayHelper::parseArrayString($resourceContent, $pattern, false);
                $resourceContent = ArrayHelper::replaceArray($resourceContent, "\n" . $selectedTable->getResourceArray($oldResource) . "\n\t\t", $pattern);

                // Write the updated code to the factory file.
                $result = FileManager::write($resourceFilePath, $resourceContent, true);

                parent::log("Resource [$resourceFilePath]", $result, 'updated');
            }
        }
    }
}
