<?php

declare(strict_types=1);

namespace src\CodeGeneration;


use src\Utils\FileManager;
use src\Helpers\StubFileProcessor;
use src\Shared\CodeGenerationContext as CGC;
use src\CodeGeneration\AbstractCodeGenerator;
use src\Config\Paths\LaravelPaths;
use src\Config\Paths\StubPaths;
use src\Helpers\NamingHelper;

/**
 * Class ServiceGenerator
 *
 * This class is responsible for generating service code.
 *
 * @package src\CodeGeneration
 */
class ServiceGenerator extends AbstractCodeGenerator
{
    /** @var array Variables used in the stub file for generating the service code. */
    private array $stubVariables;

    public function __construct()
    {
        $this->stubVariables = [
            'serviceNamespace' => LaravelPaths::SERVICE_BASE_DIRECTORY
        ];
    }

    /**
     * Generate the service code.
     *
     * This method replaces stub variables in the service stub file and writes the generated code to the specified service file path.
     * 
     * @return void
     */
    public function generate(array $params = []): void
    {
        foreach (CGC::$selectedTables as $selectedTable) {
            $className = NamingHelper::getClassName($selectedTable->tableName);

            $this->stubVariables['className'] = $className;

            $serviceFilePath = CGC::$laravelRootDirectory . LaravelPaths::SERVICE_BASE_DIRECTORY
                . '\\' . $className . 'Service.php';

            // Replace service stub variables and retrieve the final contents of the service stub file.
            $content = StubFileProcessor::replaceVariables(
                StubPaths::SERVICE_STUB_PATH,
                $this->stubVariables
            );

            // Create the directory path for the service.
            FileManager::makeDirectory(dirname($serviceFilePath));

            // Write the generated code to the service file.
            $result = FileManager::write($serviceFilePath, $content);


            // Log the result of the generation process.
            parent::log("Service [$serviceFilePath]", $result);
        }
    }
}
