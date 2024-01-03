<?php

declare(strict_types=1);

namespace src\CodeGeneration;

use src\Config\IgnoreArray;

use src\Config\Paths\LaravelPaths;
use src\Config\Paths\StubPaths;
use src\Utils\FileManager;
use src\Helpers\ArrayHelper;
use src\Helpers\ClassModifier;
use src\Helpers\NamingHelper;
use src\Helpers\StubFileProcessor;
use src\Models\Database\Structure\Table;
use src\Shared\CodeGenerationContext as CGC;


/**
 * Class RequestGenerator
 *
 * This class is responsible for generating request code.
 *
 * @package src\CodeGeneration
 */
class RequestGenerator extends AbstractCodeGenerator
{
    /** @var array Variables used in the stub file for generating the request code. */
    private array $stubVariables;


    public function __construct()
    {
        $this->stubVariables = [
            'requestNamespace'  => LaravelPaths::REQUEST_BASE_DIRECTORY
        ];
    }

    /**
     * Generate the request code.
     * 
     * This method generates a request file with validators, depending on the selected APIs.
     * The generated code is written to the specified request file path. Optionally, it can update an existing request file
     * by filling the array with the latest table columns and validators.
     *
     * @return void
     */
    public function generate(array $params = []): void
    {
        foreach (CGC::$selectedTables as $selectedTable) {
            $className = NamingHelper::getClassName($selectedTable->tableName);

            $this->stubVariables['className'] = $className;

            $requestFilePath = CGC::$laravelRootDirectory . LaravelPaths::REQUEST_BASE_DIRECTORY
                . "\\" . $className . "Request.php";

            if (!file_exists($requestFilePath)) {
                // Replace request stub variables and retrieve the final contents of the request stub file.
                $requestContent = StubFileProcessor::replaceVariables(
                    StubPaths::REQUEST_STUB_PATH,
                    $this->stubVariables
                );

                // Create the directory path for the request.
                FileManager::makeDirectory(dirname($requestFilePath));

                // Add validator functions
                $requestContent = $this->addValidatorFunctions($requestContent, $selectedTable);

                // Write the generated code to the request file.
                $result = FileManager::write($requestFilePath, $requestContent);

                // Log the result of the generation process.
                parent::log("Request [$requestFilePath]", $result);
            } else {
                $requestContent = file_get_contents($requestFilePath);

                foreach (CGC::$selectedAPIs as $api) {
                    if (in_array($api, IgnoreArray::API)) {
                        continue;
                    }
                    if (ClassModifier::functionIsExist($requestContent, $api['name'] . 'Validator')) {
                        // Pattern to find the rules array.
                        $pattern = "/(?:private|public|protected)\\s+function\\s+" . $api['name'] . 'Validator' . "\\s*\\(.*?\\)\\s*\\{.*?return\\s*(\\[(?:[^\\[\\]]++|(?1))*+\\])\\s*;\\s*\\}/s";
                        $oldArray = ArrayHelper::parseArrayString($requestContent, $pattern, false);
                        $newArray = $selectedTable->{'get' . ucfirst($api['name']) . 'ValidationRules'}($oldArray);
                        $requestContent = ArrayHelper::replaceArray($requestContent, "\n" .  $newArray . "\n\t\t", $pattern);
                    }
                }

                // Add validator functions
                $requestContent = $this->addValidatorFunctions($requestContent, $selectedTable);

                // Write the updated code to the factory file.
                $result = FileManager::write($requestFilePath, $requestContent, true);
                parent::log("Request [$requestFilePath]", $result, 'updated');
            }
        }
    }

    /**
     * Add validator functions to the request content.
     *
     * @param string $requestContent The original content of the request.
     * @param Table $selectedTable The selected table object.
     * @return string The updated request content.
     */
    private function addValidatorFunctions(string $requestContent, Table $selectedTable): string
    {
        foreach (CGC::$selectedAPIs as $api) {
            if (in_array($api, IgnoreArray::API)) {
                continue;
            }

            $columns = $selectedTable->{'get' . ucfirst($api['name']) . 'ValidationRules'}();
            $this->stubVariables['columns'] = $columns;
            $this->stubVariables['functionName'] = $api['name'];

            // Replace validator stub variables and retrieve the final contents of the validator stub file.
            $content = StubFileProcessor::replaceVariables(
                StubPaths::VALIDATION_FUNCTION_STUB_PATH,
                $this->stubVariables
            );

            if (!ClassModifier::functionIsExist($requestContent, $this->stubVariables['functionName'] . 'Validator')) {
                // Add the validator function if it is not exits
                $requestContent = ClassModifier::appendFunction($requestContent, $content);
            }
        }
        return $requestContent;
    }
}
