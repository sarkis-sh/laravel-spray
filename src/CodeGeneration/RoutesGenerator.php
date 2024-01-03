<?php

declare(strict_types=1);

namespace src\CodeGeneration;


use src\Config\Paths\LaravelPaths;
use src\Config\Paths\StubPaths;
use src\Constants\ClassPostfix;
use src\Constants\Route;
use src\Constants\NameFormatType;
use src\Helpers\NamingHelper;
use src\Utils\FileManager;
use src\Helpers\StubFileProcessor;
use src\Helpers\RouteGroupModifier;
use src\Shared\CodeGenerationContext as CGC;

/**
 * Class RoutesGenerator
 *
 * This class is responsible for generating API routes code.
 *
 * @package src\CodeGeneration
 */
class RoutesGenerator extends AbstractCodeGenerator
{
    /** @var string The file path where the generated API routes code will be written. */
    private string $apiRoutesFilePath;

    /** @var array Variables used in the stub file for generating the API routes code. */
    private array $stubVariables;


    public function __construct()
    {
        $this->apiRoutesFilePath = CGC::$laravelRootDirectory . '\\' . LaravelPaths::ROUTES_API_BASE_PATH;
    }

    /**
     * Generate the API routes code.
     *
     * This method generates a new route group in the API routes file, depending on the selected table.
     * If the route group with the same prefix already exists, it will be reused; otherwise, a new group will be created.
     * The generated routes are appended to the group based on the selected API methods.
     * Additionally, the controller's use statement is added to the API routes file.
     *
     * @return void
     */
    public function generate(array $params = []): void
    {
        $apiRoutesFileContent = file_get_contents($this->apiRoutesFilePath);

        foreach (CGC::$selectedTables as $selectedTable) {
            $className = NamingHelper::getClassName($selectedTable->tableName);
            $pluralVar = NamingHelper::getVarName($selectedTable->tableName, NameFormatType::PLURAL);

            $this->stubVariables = [
                'className' => $className,
                'pluralVar' => $pluralVar
            ];
            // Replace API routes stub variables and retrieve the final contents of the API routes stub file.
            $content = StubFileProcessor::replaceVariables(
                StubPaths::ROUTES_GROUP_STUB_PATH,
                $this->stubVariables
            );

            // Pattern to find the group with same prefix
            $pattern = '/Route::group\(\[\s*\'prefix\'\s*=>\s*\'' . preg_quote('/' . $pluralVar, '/') . '\'/m';

            // Create new Route group if it's not exist
            $apiRoutesFileContent = FileManager::append($apiRoutesFileContent, $content, $pattern, 0);

            // Add the routes to the group based on selected APIs
            foreach (CGC::$selectedAPIs as $api) {
                $route = Route::MAP[$api['name']];
                $apiRoutesFileContent = RouteGroupModifier::appendToExistingRouteGroup($apiRoutesFileContent, $pluralVar, $route);
            }

            // Add the controller use statement
            $useStatementGenerator = new UseStatementGenerator($apiRoutesFileContent);
            $apiRoutesFileContent = $useStatementGenerator->generate([
                'classNames' => [$className . ClassPostfix::CONTROLLER_POSTFIX],
                'specificDir' => LaravelPaths::CONTROLLER_BASE_DIRECTORY
            ]);
        }

        $result = FileManager::write($this->apiRoutesFilePath, $apiRoutesFileContent, true);
        parent::log('Api routes', $result, 'updated');
    }
}
