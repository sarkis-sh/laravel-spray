<?php

declare(strict_types=1);

namespace src\Services;

use src\Config\DBConfig;

use src\Config\Paths\OutPaths;
use src\Database\DatabaseFactory;
use src\Helpers\DatabaseCachManager;
use src\Helpers\EnvHelper;
use src\Helpers\LaravelHelper;
use src\Models\Database\Structure\Database;
use src\Services\Http\PostmanService;
use src\Shared\CodeGenerationContext as CGC;
use src\Utils\FileManager;

/**
 * Class UserProjectsService
 * A class to manage user projects and perform various operations on them
 * 
 * @package src\Services
 */
class UserProjectsService
{
    /**  @var array The user projects */
    public array $userProjects;

    /** @var PostmanService Provides methods for interacting with Postman.. */
    private PostmanService $postmanService;

    public function __construct(PostmanService $postmanService)
    {
        $this->postmanService = $postmanService;
    }
    /**
     * Sets the user projects by reading them from a file
     *
     * @return void
     */
    public function setUserProjects(): void
    {
        $content = file_get_contents(OutPaths::USER_PROJECTS_FILE);

        $this->userProjects = json_decode($content, true);
    }

    /**
     * Selects a project by setting the Laravel root directory, environment variables,
     * database configuration, and database cache manager
     *
     * @param string $laravelRootDirectory The Laravel root directory
     * @return void
     */
    public function selectProject(string $laravelRootDirectory): void
    {
        CGC::setLaravelRootDirectory($laravelRootDirectory);

        EnvHelper::setEnv($laravelRootDirectory);

        DBConfig::init();

        $database = DatabaseFactory::create();

        DatabaseCachManager::update($database, $this);

        CGC::setDatabase($database);
    }

    /**
     * Adds a new project or updates an existing project in the user projects file
     *
     * @param string $laravelRootDirectory The Laravel root directory
     * @return string|false Project name if the project was added or updated successfully, false otherwise
     */
    public function addNewProjectOrUpdate(string $laravelRootDirectory)
    {
        FileManager::write(OutPaths::USER_PROJECTS_FILE, '');

        // Replace slashes with double slashes
        $laravelRootDirectory = preg_replace('/\\\\+/', '\\\\\\\\', $laravelRootDirectory);

        // Check if the string doesn't end with a double slash, then add it
        if (substr($laravelRootDirectory, -2) !== '\\\\') {
            $laravelRootDirectory .= '\\\\';
        }

        $projectName = $this->getProjectName($laravelRootDirectory);

        if ($projectName == false) {
            return false;
        }

        $content = file_get_contents(OutPaths::USER_PROJECTS_FILE);

        $jsonContent = json_decode($content, true);

        $isNew = false;

        $requestBodyType = [];
        if (!isset($jsonContent[$projectName])) {
            $isNew = true;
            $requestBodyType = [
                'bulk_store' => 'RAW',
                'store'      => 'RAW',
            ];
        }

        $jsonContent[$projectName] = [
            "path"      => stripcslashes($laravelRootDirectory),
            "is_new"    => $isNew,
            "request"   => $requestBodyType
        ];

        $jsonData = json_encode($jsonContent, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        file_put_contents(OutPaths::USER_PROJECTS_FILE, $jsonData);

        $this->userProjects = $jsonContent;
        return $projectName;
    }

    /**
     * Get the project name from the given directory.
     *
     * @param string $dir The directory path.
     * @return string|false The project name if it is a Laravel directory, false otherwise.
     */
    public function getProjectName(string $dir)
    {
        $isLaravelDirectory = LaravelHelper::isLaravelDirectory($dir);

        if (!$isLaravelDirectory) {
            return false;
        }

        return pathinfo($dir)['filename'];
    }

    /**
     * Update the Postman information for a project.
     *
     * @param string $projectName The name of the project.
     * @param string $collectionId The collection ID in Postman.
     * @param string $apiKey The API key for Postman.
     * @return bool True if the information was updated successfully, false otherwise.
     */
    public function updatePostmanInformation(string $projectName, string $collectionId, string $apiKey)
    {
        $response = $this->postmanService->getCollection($collectionId, $apiKey);

        if ($response->status !== 'Success') {
            return false;
        } else {
            $content = file_get_contents(OutPaths::USER_PROJECTS_FILE);

            $jsonContent = json_decode($content, true);

            $jsonContent[$projectName]['postman']['collection_id'] = $collectionId;
            $jsonContent[$projectName]['postman']['x-api-key'] = $apiKey;

            $jsonData = json_encode($jsonContent, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

            file_put_contents(OutPaths::USER_PROJECTS_FILE, $jsonData);

            return true;
        }
    }

    /**
     * Deletes a project from the user projects file
     *
     * @param string $projectName The name of the project to delete
     * @return bool True if the project was deleted successfully, false otherwise
     */
    public function deleteProject(string $projectName): bool
    {
        $content = file_get_contents(OutPaths::USER_PROJECTS_FILE);

        $jsonContent = json_decode($content, true);

        unset($jsonContent[$projectName]);

        $jsonData = json_encode($jsonContent, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        file_put_contents(OutPaths::USER_PROJECTS_FILE, $jsonData);

        FileManager::deleteDirectory(OutPaths::DB_CACH_DIRECTORY . $projectName);

        return true;
    }

    /**
     * Updates the tables status for a project by comparing the old and new databases
     *
     * @param Database $oldDB The old database
     * @param Database $newDB The new database
     * @param string $projectName The name of the project
     * @return void
     */
    public function updateTablesStatus(Database $oldDB, Database $newDB, string $projectName): void
    {
        $list = self::compareTables($oldDB, $newDB);
        if (!empty($list)) {
            $content = file_get_contents(OutPaths::USER_PROJECTS_FILE);

            $jsonContent = json_decode($content, true);

            foreach ($list as $key => $value) {
                $jsonContent[$projectName]['tables_status'][$key] = $value;
            }

            $jsonData = json_encode($jsonContent, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

            file_put_contents(OutPaths::USER_PROJECTS_FILE, $jsonData);
        }
    }

    /**
     * Compares the tables of the old and new databases and returns a list of modified and new tables
     *
     * @param Database $oldDB The old database
     * @param Database $newDB The new database
     * @return array The list of modified and new tables
     */
    private function compareTables(Database $oldDB, Database $newDB): array
    {
        $result = [];

        $oldTables = $oldDB->tables;
        $newTables = $newDB->tables;
        // Create associative arrays
        $oldAssoc = array_combine(array_column($oldTables, 'tableName'), $oldTables);
        $newAssoc = array_combine(array_column($newTables, 'tableName'), $newTables);

        // Compare and identify updated and added tables
        foreach ($newAssoc as $key => $newObj) {
            if (isset($oldAssoc[$key])) {
                // Table exists in both arrays, check for updates
                $oldObj = $oldAssoc[$key];
                if (!$newObj->equals($oldObj)) {
                    $result[$key] = 'MODIFIED';
                }
            } else {

                // Table is present only in the new array
                $result[$key] = 'NEW';
            }
        }

        return $result;
    }

    /**
     * Gets the tables status for a project
     *
     * @param string $projectName The name of the project
     * @return array The tables status for the project
     */
    public function getTablesStatus(string $projectName): array
    {
        $content = file_get_contents(OutPaths::USER_PROJECTS_FILE);

        $jsonContent = json_decode($content, true);

        if (isset($jsonContent[$projectName]['tables_status'])) {
            return $jsonContent[$projectName]['tables_status'];
        } else {
            return [];
        }
    }

    /**
     * Deletes the status of a table for a project
     *
     * @param string $projectName The name of the project
     * @param string $tableName The name of the table
     * @return void
     */
    public function deleteTableStatus(string $projectName, string $tableName): void
    {
        $content = file_get_contents(OutPaths::USER_PROJECTS_FILE);

        $jsonContent = json_decode($content, true);

        if (isset($jsonContent[$projectName]['tables_status'][$tableName]))
            unset($jsonContent[$projectName]['tables_status'][$tableName]);

        $jsonData = json_encode($jsonContent, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        file_put_contents(OutPaths::USER_PROJECTS_FILE, $jsonData);
    }

    /**
     * Marks a project as old by updating its "is_new" status
     *
     * @param string $projectName The name of the project
     * @return void
     */
    public function markProjectAsOld(string $projectName): void
    {
        $content = file_get_contents(OutPaths::USER_PROJECTS_FILE);

        $jsonContent = json_decode($content, true);

        $jsonContent[$projectName]['is_new'] = false;

        $jsonData = json_encode($jsonContent, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        file_put_contents(OutPaths::USER_PROJECTS_FILE, $jsonData);
    }

    /**
     * Get Postman information for a project
     * @param string $projectName The name of the project
     * @return array The Postman information (Collection ID, X-Api-Key)
     */
    public function getPostmanInfo(string $projectName)
    {
        $content = file_get_contents(OutPaths::USER_PROJECTS_FILE);

        $jsonContent = json_decode($content, true);

        return isset($jsonContent[$projectName]['postman']) ? $jsonContent[$projectName]['postman'] : null;
    }

    /**
     * Update the request body type for a specific project in the user projects file.
     *
     * @param string $projectName The name of the project.
     * @param array $requestsBodyTypes The updated request body types for the project.
     * @return void
     */
    public function updateRequestBodyType(string $projectName, array $requestsBodyTypes)
    {
        $content = file_get_contents(OutPaths::USER_PROJECTS_FILE);

        $jsonContent = json_decode($content, true);

        $jsonContent[$projectName]['request'] = $requestsBodyTypes;

        $jsonData = json_encode($jsonContent, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        file_put_contents(OutPaths::USER_PROJECTS_FILE, $jsonData);
    }
}
