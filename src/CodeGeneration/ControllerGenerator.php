<?php

declare(strict_types=1);

namespace src\CodeGeneration;


use src\Utils\FileManager;
use src\Helpers\StubFileProcessor;
use src\Shared\CodeGenerationContext as CGC;
use src\Config\Paths\LaravelPaths;
use src\Config\Paths\StubPaths;
use src\Constants\NameFormatType;
use src\Helpers\NamingHelper;

/**
 * Class ControllerGenerator
 *
 * This class is responsible for generating controller code.
 *
 * @package src\CodeGeneration
 */
class ControllerGenerator extends AbstractCodeGenerator
{
    /** @var array Variables used in the stub file for generating the controller code. */
    private array $stubVariables;

    public function __construct()
    {
        $this->stubVariables = [
            'controllerNamespace'   => LaravelPaths::CONTROLLER_BASE_DIRECTORY,
            'requestPrefix'         => LaravelPaths::REQUEST_BASE_DIRECTORY,
            'resourcePrefix'        => LaravelPaths::RESOURCE_BASE_DIRECTORY,
            'servicePrefix'         => LaravelPaths::SERVICE_BASE_DIRECTORY,
            'modelPrefix'           => LaravelPaths::MODEL_BASE_DIRECTORY
        ];
    }

    /**
     * Generate a controller code.
     *
     * This method replaces stub variables in the controller stub file and writes the generated code to the specified controller file path.
     *
     * @return void
     */
    public function generate(array $params = []): void
    {
        foreach (CGC::$selectedTables as $selectedTable) {
            $className = NamingHelper::getClassName($selectedTable->tableName);
            $singularVar = NamingHelper::getVarName($selectedTable->tableName, NameFormatType::SINGULAR);

            $this->stubVariables = array_merge($this->stubVariables, [
                'className'             => $className,
                'singularVar'           => $singularVar
            ]);

            $controllerFilePath = CGC::$laravelRootDirectory . LaravelPaths::CONTROLLER_BASE_DIRECTORY . '\\' .
                $className . 'Controller.php';

            // Replace controller stub variables and retrieve the final contents of the controller stub file.
            $content = StubFileProcessor::replaceVariables(
                StubPaths::CONTROLLER_STUB_PATH,
                $this->stubVariables
            );

            // Create the directory path for the controller.
            FileManager::makeDirectory(dirname($controllerFilePath));

            // Write the generated code to the controller file.
            $result = FileManager::write($controllerFilePath, $content);

            // Log the result of the generation process.
            parent::log("Controller [$controllerFilePath]", $result);
        }
    }
}
