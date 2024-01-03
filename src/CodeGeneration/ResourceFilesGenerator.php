<?php

declare(strict_types=1);

namespace src\CodeGeneration;


use src\Config\Paths\LaravelPaths;
use src\Helpers\ArrayHelper;
use src\Helpers\ResourceFileManager;
use src\Utils\FileManager;
use src\Shared\CodeGenerationContext as CGC;


/**
 * Class ResourceFilesGenerator
 *
 * This class is responsible for copying resource files used in code generation.
 * 
 * @package src\CodeGeneration
 */
class ResourceFilesGenerator extends AbstractCodeGenerator
{
    public function __construct()
    {
    }

    /**
     * This method copies and updates resource files.
     */
    public function generate(array $params = []): void
    {
        $result = ResourceFileManager::copyResourceFiles(CGC::$laravelRootDirectory);

        $this->updateLangFiles();

        // Log the result of the generation process.
        parent::log("Resource files", $result, 'copied');
    }

    /**
     * Update the language files with model names and table columns.
     *
     * This function updates the models.php and validation.php files in the language directory with model names
     * and table columns respectively. It retrieves the model names and table columns using various helper methods.
     * The updated files will be saved in the corresponding language directories (e.g., ar/models.php, en/validation.php).
     *
     * @return void
     */
    public function updateLangFiles(): void
    {
        $modelsDirectory = CGC::$laravelRootDirectory . LaravelPaths::MODEL_BASE_DIRECTORY;
        $baseLangPath = CGC::$laravelRootDirectory . LaravelPaths::LANG_BASE_DIRECTORY;

        // Update model names in models.php files ar/en
        $this->updateModelLangFile($baseLangPath, 'ar', $modelsDirectory);
        $this->updateModelLangFile($baseLangPath, 'en', $modelsDirectory);

        // Update table columns in validation.php files ar/en
        $this->updateValidationLangFile($baseLangPath, 'ar');
        $this->updateValidationLangFile($baseLangPath, 'en');
    }

    /**
     * Update the model names in the models.php file of the specified language.
     *
     * @param string $baseLangPath The base path of the language directory.
     * @param string $language The language code.
     * @param string $modelsDirectory The directory containing the model files.
     * @return void
     */
    private function updateModelLangFile(string $baseLangPath, string $language, string $modelsDirectory): void
    {
        $modelsPath = $baseLangPath . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . 'models.php';
        $result = false;
        if (file_exists($modelsPath)) {
            $modelsContent = file_get_contents($modelsPath);
            $modelPattern = '/return\\s*(\\[(?:[^\\[\\]]++|(?1))*+\\])\\s*;/s';
            $existingModelNames = ArrayHelper::parseArrayString($modelsContent, $modelPattern, false);
            $newModelNames = FileManager::getAllFileNamesInDirectory($modelsDirectory, true, ['GenericModel'], $existingModelNames, $language);
            $updatedModelsContent = ArrayHelper::replaceArray($modelsContent, "\n" . $newModelNames . "\n", $modelPattern);
            $result = FileManager::write($modelsPath, $updatedModelsContent, true);
        }
        parent::log("Lang [$modelsPath]", $result, 'updated');
    }

    /**
     * Update the table columns in the validation.php file of the specified language.
     *
     * @param string $baseLangPath The base path of the language directory.
     * @param string $language The language code.
     * @return void
     */
    private function updateValidationLangFile(string $baseLangPath, string $language): void
    {
        $validationPath = $baseLangPath . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . 'validation.php';
        $result = false;
        if (file_exists($validationPath)) {
            $validationContent = file_get_contents($validationPath);
            $attrPattern = '/\'attributes\'\s*=>\\s*(\\[(?:[^\\[\\]]++|(?1))*+\\])\\s*,/s';
            $existingAttributes = ArrayHelper::parseArrayString($validationContent, $attrPattern, false);
            $newAttributes = CGC::$database->getValidationAttributesArray($existingAttributes, $language);
            $updatedValidationContent = ArrayHelper::replaceArray($validationContent, "\n" . $newAttributes . "\n\t", $attrPattern);
            $result = FileManager::write($validationPath, $updatedValidationContent, true);
        }
        parent::log("Lang [$validationPath]", $result, 'updated');
    }
}
