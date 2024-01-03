<?php

declare(strict_types=1);

namespace src\CodeGeneration;

use src\Utils\FileManager;
use src\Helpers\ArrayHelper;
use src\Config\Paths\StubPaths;
use src\Helpers\StubFileProcessor;
use src\Config\Paths\LaravelPaths;
use src\Helpers\NamingHelper;
use src\Models\Database\Structure\Table;
use src\Shared\CodeGenerationContext as CGC;

/**
 * Class ModelGenerator
 *
 * This class is responsible for generating model code.
 *
 * @package src\CodeGeneration
 */
class ModelGenerator extends AbstractCodeGenerator
{
    /** @var array Variables used in the stub file for generating the model code. */
    private array $stubVariables;


    public function __construct()
    {
        $this->stubVariables = [
            'modelNamespace'  => LaravelPaths::MODEL_BASE_DIRECTORY
        ];
    }

    /**
     * Generate the model code.
     * 
     * This method generates a model with all possible relations and fills the fillable array with table columns.
     * The generated code is written to the specified model file path. Optionally, it can update an existing model file 
     * by filling the array with the latest table columns and add newest relations.
     *
     * @return void
     */
    public function generate(array $params = []): void
    {
        foreach (CGC::$selectedTables as $selectedTable) {
            $className = NamingHelper::getClassName($selectedTable->tableName);

            $this->stubVariables['className'] = $className;


            $this->stubVariables = array_merge($this->stubVariables, [
                'tableName'       => $selectedTable->tableName,
                'columns'         => $selectedTable->getModelFillable(),
                'className'       => $className,
                'otherProperties' => $this->getTimestampsStatusProperties($selectedTable)
            ]);

            $modelFilePath = CGC::$laravelRootDirectory . LaravelPaths::MODEL_BASE_DIRECTORY . '\\' . $className . '.php';


            if (!file_exists($modelFilePath)) {
                // Replace model stub variables and retrieve the final contents of the model stub file.
                $content = StubFileProcessor::replaceVariables(
                    StubPaths::MODEL_STUB_PATH,
                    $this->stubVariables
                );
                // Create the directory path for the model.
                FileManager::makeDirectory(dirname($modelFilePath));
                $content = $this->addRelations($content, $selectedTable);
                // Write the generated code to the model file.
                $result = FileManager::write($modelFilePath, $content);

                // Log the result of the generation process.
                parent::log("Model [$modelFilePath]", $result);
            } else {
                $content = file_get_contents($modelFilePath);
                $pattern = '/protected\s+\$fillable\s*=\\s*(\\[(?:[^\\[\\]]++|(?1))*+\\])\\s*;/s';
                $content = ArrayHelper::replaceArray($content, "\n" . $selectedTable->getModelFillable() . "\n\t", $pattern);

                $content = $this->addRelations($content, $selectedTable);
                $content = $this->updateTimestampsStatusProperties($selectedTable, $content);
                // Write the updated code to the model file.
                $result = FileManager::write($modelFilePath, $content, true);

                parent::log("Model [$modelFilePath]", $result, 'updated');
            }

            //Update lang files
            $resourceFileGenerator = new ResourceFilesGenerator();
            $resourceFileGenerator->updateLangFiles();
        }
    }

    /**
     * Adds the relations to the given content for the selected table.
     *
     * @param string $content The content to which relations will be added.
     * @param Table $selectedTable The selected table for which relations will be added.
     * @return string The modified content with added relations.
     */
    private function addRelations(string $content, Table $selectedTable)
    {
        $relationGenerator = new RelationsGenerator($content);
        return $relationGenerator->generate(['selectedTable' => $selectedTable]);
    }

    /**
     * Get the timestamps status properties for the given table.
     *
     * @param Table $table
     * @return string
     */
    private function getTimestampsStatusProperties(Table $table): string
    {
        $timestampProperties = [];
        if (!$table->use_created_at) {
            $timestampProperties[] = "\tconst CREATED_AT = null;";
        }
        if (!$table->use_updated_at) {
            $timestampProperties[] = "\tconst UPDATED_AT = null;";
        }

        return empty($timestampProperties) ? '' : implode("\n\n", $timestampProperties);
    }

    /**
     * Update the timestamps status properties in the given content based on the given table.
     *
     * @param Table $table
     * @param string $content
     * @return string
     */
    private function updateTimestampsStatusProperties(Table $table, string $content): string
    {
        $createdAtPattern = '/.*const\s+CREATED_AT\s*=\s*null\s*;.*\n\n?/';
        $updatedAtPattern = '/.*const\s+UPDATED_AT\s*=\s*null\s*;.*\n\n?/';

        $content = preg_replace($createdAtPattern, '', $content);
        $content = preg_replace($updatedAtPattern, '', $content);

        $lines = explode("\n", $content);

        $newContent = '';

        $timestampProperties = $this->getTimestampsStatusProperties($table);
        foreach ($lines as $line) {
            if (preg_match('/^\s*protected\s*\$fillable/', $line)) {
                if ($timestampProperties != '') {
                    $newContent .=  "$timestampProperties\n\n";
                }
            }
            $newContent .= "$line\n";
        }

        return $newContent;
    }
}
