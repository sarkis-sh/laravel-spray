<?php

declare(strict_types=1);

namespace src\CodeGeneration;


use src\Config\Paths\LaravelPaths;
use src\Config\Paths\StubPaths;
use src\Helpers\ArrayHelper;
use src\Helpers\NamingHelper;
use src\Utils\FileManager;
use src\Helpers\StubFileProcessor;
use src\Shared\CodeGenerationContext as CGC;

/**
 * Class FactoryGenerator
 *
 * This class is responsible for generating factory code.
 *
 * @package src\CodeGeneration
 */
class FactoryGenerator extends AbstractCodeGenerator
{
    /** @var array Variables used in the stub file for generating the factory code. */
    private array $stubVariables;

    /** @var array An array containing the formatted faker array and an array of associated models. */
    private array $fakerArray;


    public function __construct()
    {

        $this->stubVariables = [
            'factoryNamespace'  => LaravelPaths::FACTORY_BASE_DIRECTORY
        ];
    }


    /**
     * Generate the factory code.
     * 
     * This method replaces stub variables in the factory stub file with table columns and corresponding faker data for each column.
     * The generated code is then written to the specified factory file path. Optionally, it can update an existing factory file 
     * by filling the array with the latest table columns and corresponding faker data.
     *
     * @return void
     */
    public function generate(array $params = []): void
    {
        foreach (CGC::$selectedTables as $selectedTable) {
            $className = NamingHelper::getClassName($selectedTable->tableName);

            $this->stubVariables['className'] = $className;

            $factoryFilePath = CGC::$laravelRootDirectory  . LaravelPaths::FACTORY_BASE_DIRECTORY .
                '\\' . $className . 'Factory.php';

            if (!file_exists($factoryFilePath)) {
                $this->fakerArray = $selectedTable->getFakerArray();

                $this->stubVariables['columns'] = $this->fakerArray['faker'];

                // Replace factory stub variables and retrieve the final contents of the factory stub file.
                $content = StubFileProcessor::replaceVariables(
                    StubPaths::FACTORY_STUB_PATH,
                    $this->stubVariables
                );

                // Create the directory path for the factory.
                FileManager::makeDirectory(dirname($factoryFilePath));

                $content = $this->addUseStatements($content);

                // Write the generated code to the factory file.
                $result = FileManager::write($factoryFilePath, $content);

                // Log the result of the generation process.
                parent::log("Factory [$factoryFilePath]", $result);
            } else {
                $content = file_get_contents($factoryFilePath);

                $pattern = "/(?:private|public|protected)\\s+function\\s+definition\\s*\\(.*?\\)\\s*\\{.*?return\\s*(\\[(?:[^\\[\\]]++|(?1))*+\\])\\s*;\\s*\\}/s";
                $oldArray = ArrayHelper::parseArrayString($content, $pattern, false);

                $this->fakerArray = $selectedTable->getFakerArray($oldArray);

                $newFakerArray = $this->fakerArray['faker'];

                $content = ArrayHelper::replaceArray($content, "\n" .  $newFakerArray . "\n\t\t", $pattern);

                $content = $this->addUseStatements($content);

                // Write the updated code to the factory file.
                $result = FileManager::write($factoryFilePath, $content, true);

                parent::log("Factory [$factoryFilePath]", $result, 'updated');
            }
        }
    }

    /**
     * Adds the use statements to the given content.
     *
     * @param string $content The content to which use statements will be added.
     * @return string The modified content with added use statements.
     */
    private function addUseStatements(string $content): string
    {
        /** @var array $models */
        $models = $this->fakerArray['models'];

        // Add models use statements if there is a foriegn key faker
        $useStatementGenerator = new UseStatementGenerator($content);

        $result = $useStatementGenerator->generate([
            'classNames' => $models,
            'specificDir' => LaravelPaths::MODEL_BASE_DIRECTORY
        ]);

        return $result;
    }
}
