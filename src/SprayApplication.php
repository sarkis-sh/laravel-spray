<?php

declare(strict_types=1);

namespace src;

use ReflectionClass;
use src\Constants\Color;
use src\Constants\API;

use src\Helpers\ConsoleHelper as CH;

use src\Utils\FileManager;

use src\CodeGeneration\ModelGenerator;
use src\CodeGeneration\RoutesGenerator;
use src\CodeGeneration\FactoryGenerator;
use src\CodeGeneration\RequestGenerator;
use src\CodeGeneration\ServiceGenerator;
use src\CodeGeneration\ResourceGenerator;
use src\CodeGeneration\ControllerGenerator;
use src\CodeGeneration\Postman\CollectionGenerator;
use src\CodeGeneration\ResourceFilesGenerator;
use src\Config\Paths\OutPaths;
use src\Services\Http\PostmanService;
use src\Services\UserProjectsService;
use src\Shared\CodeGenerationContext as CGC;

/**
 * Class SprayApplication
 * 
 * @package src
 */
class SprayApplication
{

    /** @var UserProjectsService perform various functionalities related to user projects. */
    private UserProjectsService $userProjectsService;

    /** @var PostmanService Provides methods for interacting with Postman.. */
    private PostmanService $postmanService;

    /** @var array array of all available options that can be selected by the user in the console. */
    private array $availableOptions;

    public function __construct()
    {
        FileManager::write(OutPaths::USER_PROJECTS_FILE, json_encode([]));
        $this->postmanService = new PostmanService();
        $this->userProjectsService = new UserProjectsService($this->postmanService);
        $this->userProjectsService->setUserProjects();
    }

    /**
     * Start the application.
     *
     * This method serves as the entry point for the application, guiding the user through various stages
     * such as project addition, project selection, and table selection.
     * 
     * @return void 
     */
    public function start(string $currentFolder): void
    {
        $stageStatus = true;
        do {
            if (!$stageStatus) {
                CH::addText("\n<INVALID INPUT>\n", Color::RED);
            }

            $projectName = $this->userProjectsService->getProjectName($currentFolder);

            if ($projectName != false) {
                if (isset($this->userProjectsService->userProjects[$projectName])) {
                    $this->setProject($projectName);
                    $this->tableSelectionStage();
                } else {
                    $this->projectAdditionStage($currentFolder);
                }
            } elseif (empty($this->userProjectsService->userProjects)) {
                $status = $this->projectAdditionStage();
                if ($status === false) {
                    $stageStatus = false;
                    continue;
                }
                $this->setProject(0);
                $this->tableSelectionStage();
                return;
            } else {
                $this->projectSelectionStage();
                return;
            }
        } while (true);
    }

    /**
     * Project selection stage.
     *
     * This method guides the user through the project selection stage, allowing them to select an existing
     * project, delete a project, add a new project, or paste a new Laravel path. It uses the ConsoleHelper
     * to display information and gather user input.
     * 
     * @return void
     */
    private function projectSelectionStage(): void
    {
        $stageStatus = true;

        do {
            $this->showProjects();
            if (!$stageStatus) {
                CH::addText("\n<INVALID INPUT>\n", Color::RED);
            }

            CH::addText(">> Select the project (", Color::GREEN);
            CH::addText("Delete project: ", Color::GREEN);
            CH::addText("-d", Color::RED);
            CH::addText(", Update postman info: ", Color::GREEN);
            CH::addText("-up", Color::RED);
            CH::addText(", Update requests body type: ", Color::GREEN);
            CH::addText("-ur", Color::RED);
            CH::addText(") or paste new Laravel path:\n", Color::GREEN);
            CH::apply();

            $input = $this->getProjectStageInput();
            $todo = $input['todo'];
            $data = $input['data'];


            if ($todo == 'select') {
                $this->setProject($data - 1);
                $this->tableSelectionStage();
                return;
            } elseif ($todo == 'delete') {
                $projectName = array_keys($this->userProjectsService->userProjects)[$data - 1];
                $this->setProject($projectName);
                $this->userProjectsService->deleteProject($projectName);
                $this->userProjectsService->setUserProjects();
            } elseif ($todo == 'updatePostmanInfo') {
                $projectName = array_keys($this->userProjectsService->userProjects)[$data - 1];
                $this->setProject($projectName);
                $this->postmanAdditionStage($projectName);
                $this->userProjectsService->setUserProjects();
            } elseif ($todo == 'updateRequestBodyTypes') {
                $projectName = array_keys($this->userProjectsService->userProjects)[$data - 1];
                $this->setProject($projectName);
                $this->updateRequestBodyTypeStage($projectName);
                $this->userProjectsService->setUserProjects();
            } elseif ($todo == 'add') {
                $result = $this->userProjectsService->addNewProjectOrUpdate($data);
                if ($result === false) {
                    $stageStatus = false;
                } else {
                    $size = sizeof($this->userProjectsService->userProjects) - 1;
                    $this->setProject($size);

                    $this->postmanAdditionStage($result);

                    $this->tableSelectionStage();
                    return;
                }
            } else {
                $stageStatus = false;
            }
        } while (true);
    }

    /**
     * Get user input for the project selection stage.
     *
     * This method reads user input from the console, validates and processes it, and returns an array
     * containing the action to perform ('select', 'delete', 'add', or 'refresh') and the corresponding data.
     *
     * @return array
     *   An associative array with keys:
     *   - 'todo': The action to perform ('select', 'delete', 'add', or 'refresh').
     *   - 'data': The data associated with the action (e.g., project index, path, or 'none' for refresh).
     */
    private function getProjectStageInput(): array
    {
        $input = CH::read();

        if ($input === true) {
            die;
        }

        if (
            sizeof($input['numbers']) == 1 &&
            $input['numbers'][0] <= sizeof($this->userProjectsService->userProjects) &&
            $input['numbers'][0] > 0
        ) {
            if (
                sizeof($input['options']) == 1 &&
                $input['options'][0] == '-d'
            ) {
                $todo = 'delete';
            } elseif (
                sizeof($input['options']) == 1 &&
                $input['options'][0] == '-up'
            ) {
                $todo = 'updatePostmanInfo';
            } elseif (
                sizeof($input['options']) == 1 &&
                $input['options'][0] == '-ur'
            ) {
                $todo = 'updateRequestBodyTypes';
            } else {
                $todo = 'select';
            }

            $data = $input['numbers'][0];
        } elseif (is_dir($input['original'])) {
            $todo = 'add';
            $data = $input['original'];
        } else {
            $todo = 'refresh';
            $data = 'none';
        }

        return [
            'todo'  => $todo,
            'data'  => $data
        ];
    }

    /**
     * Set the selected project based on the provided index.
     *
     * This method takes the index of the selected project, retrieves the project name and data from
     * the user projects service, sets the selected project path, and updates the user projects list.
     * If the selected project is marked as new, it generates resource files and marks the project as old.
     *
     * @param int|string $selectedProject The index or name of the selected project in the user projects list.
     * 
     * @return void
     */
    private function setProject($selectedProject): void
    {
        if (is_int($selectedProject)) {
            $projectName = array_keys($this->userProjectsService->userProjects)[$selectedProject];
        } else {
            $projectName = $selectedProject;
        }
        $selectedProject = $this->userProjectsService->userProjects[$projectName];
        $this->userProjectsService->selectProject($selectedProject['path']);
        $this->userProjectsService->setUserProjects();

        if ($selectedProject['is_new']) {
            (new ResourceFilesGenerator())->generate();
            $this->userProjectsService->markProjectAsOld($projectName);
        }
    }

    /**
     * Table selection stage.
     *
     * This method guides the user through the table selection stage, allowing them to select tables,
     * choose API options, and generates code based on the selected tables and APIs. It utilizes
     * ConsoleHelper to display information, gather user input, and CodeGenerationContext (CGC) to
     * maintain the code generation context.
     * 
     * @return void
     */
    private function tableSelectionStage(): void
    {
        $stageStatus = true;

        do {
            $this->showTables();
            if (!$stageStatus) {
                CH::addText("\n<INVALID INPUT>\n", Color::RED);
            }

            $this->showOptions();
            CH::addText(">> Select the tables you want:\n", Color::GREEN);
            CH::apply();

            $input = $this->getTableStageInput();

            if ($input !== false) {
                $selectedTables = $input['selectedTables'];
                $selectedAPIs = $input['selectedApis'];

                CGC::init($selectedTables, $selectedAPIs);

                (new ControllerGenerator())->generate();
                (new ServiceGenerator())->generate();
                (new RoutesGenerator())->generate();
                (new ModelGenerator())->generate();
                (new RequestGenerator())->generate();
                (new ResourceGenerator())->generate();
                (new FactoryGenerator())->generate();
                (new CollectionGenerator(
                    $this->userProjectsService,
                    $this->postmanService
                ))->generate();

                foreach ($selectedTables as $selectedTable) {
                    $this->userProjectsService->deleteTableStatus(CGC::$projectName, $selectedTable->tableName);
                }

                $this->showGenerationStatus();

                $stageStatus = true;
            } else {
                $stageStatus = false;
            }
        } while (true);
    }


    /**
     * Display the generation status after completing the code generation process.
     *
     * @return void
     */
    private function showGenerationStatus(): void
    {
        $log = "- You can review the [" . CGC::$logDirectory . "\\spray_generation.log] file. ";
        CH::addText(str_pad("", strlen($log), '-', STR_PAD_BOTH) . "\n", Color::LIGHT_WHITE);
        CH::addText(str_pad("- The generation process has been completed. ", strlen($log), '-', STR_PAD_RIGHT) . "\n", Color::LIGHT_WHITE);
        CH::addText($log . "\n", Color::LIGHT_WHITE);
        CH::addText(str_pad("", strlen($log), '-', STR_PAD_BOTH) . "\n", Color::LIGHT_WHITE);
    }

    /**
     * Get user input for the table selection stage.
     *
     * This method reads user input from the console, validates and processes it, and returns an array
     * containing the selected tables and APIs based on user input.
     *
     * @return array|false
     *   An associative array with keys:
     *   - 'selectedTables': An array of selected tables.
     *   - 'selectedApis': An array of selected APIs.
     *   Returns false if the input is invalid.
     */
    private function getTableStageInput()
    {
        $tables = CGC::$database->tables;

        $maxInput = sizeof($tables);

        $input = CH::read();

        if ($input === true) {
            $this->projectSelectionStage();
        }

        $selectedTableIndexes = $input['numbers'];
        $options = $input['options'];

        if ((in_array('-a', $options))) {
            $options = $this->availableOptions;
        }

        $validTableIndexes = range(1, $maxInput);

        if (sizeof($selectedTableIndexes) == 1 && $selectedTableIndexes[0] == $maxInput + 1) {
            $selectedTableIndexes = $validTableIndexes;
        }

        if (
            (empty($selectedTableIndexes) && empty($options)) ||
            !empty(array_diff($options, array_merge($this->availableOptions))) ||
            !empty(array_diff($selectedTableIndexes, $validTableIndexes))
        ) {
            return false;
        }

        $selectedTables = array_map(fn ($index) => array_values($tables)[$index - 1], $selectedTableIndexes);

        // Get the constants of the API class using reflection
        $reflection = new ReflectionClass(API::class);
        $constants = $reflection->getConstants();

        // Filter the constants based on the option_abr values
        $selectedAPIs = array_values(array_filter($constants, function ($constant) use ($options) {
            if (isset($constant['option_abr']))
                return in_array($constant['option_abr'], $options);
        }));

        return [
            'selectedTables' => $selectedTables,
            'selectedApis'  => $selectedAPIs
        ];
    }

    /**
     * Display the list of user projects.
     * 
     * @return void
     */
    private function showProjects(): void
    {
        CH::addText("• Press Enter To Exit..\n", Color::RED);
        $index = 1;
        foreach ($this->userProjectsService->userProjects as $projectName => $data) {
            CH::addText("• $index: ", Color::LIGHT_BLUE);
            CH::addText("$projectName\n");
            $index++;
        }
    }

    /**
     * Display the list of available API options.
     *
     * @return void
     */
    private function showOptions(): void
    {
        $ApiReflection = new ReflectionClass(API::class);
        $apiConstants = $ApiReflection->getConstants();

        CH::addText("\n" . str_pad("- API List ", 61, '-', STR_PAD_RIGHT) . "\n", Color::WHITE);

        $count = 1;
        foreach ($apiConstants as $key => $value) {
            CH::addText($value['option_name'] . ": ", Color::GREEN);
            CH::addText($value['option_abr'], Color::RED);
            if ($count == 4) {
                CH::addText("\n");
            } else {
                CH::addText(" | ");
            }

            $this->availableOptions[] = $value['option_abr'];
            $count++;
        }

        CH::addText("All APIs: ", Color::GREEN);
        CH::addText("-a\n", Color::RED);

        CH::addText(str_pad("", 61, '-', STR_PAD_BOTH) . "\n");
    }

    /**
     * Display the list of tables for the selected project.
     *
     * @return void
     */
    private function showTables(): void
    {
        CH::addText("\n\n" . str_pad("- " . CGC::$projectName . " Tables ", 51, '=', STR_PAD_RIGHT) . "\n");
        $changingList =  $this->userProjectsService->getTablesStatus(CGC::$projectName);
        $index = 1;

        CH::addText("\n• Press Enter To Go Back..\n", Color::RED);

        foreach (CGC::$database->tables as $table) {
            $tableName = $table->tableName;
            $color = Color::WHITE;
            $indexColor = Color::LIGHT_BLUE;
            if (isset($changingList[$tableName])) {
                $status = $changingList[$tableName];
                if ($status == 'MODIFIED') {
                    $color = Color::YELLOW;
                    $indexColor = Color::YELLOW;
                } elseif ($status == 'NEW') {
                    $color = Color::GREEN;
                    $indexColor = Color::GREEN;
                }
            }
            CH::addText("• $index: ", $indexColor);

            CH::addText($table->tableName . "\n", $color);
            $index++;
        }

        CH::addText("• $index: ", Color::LIGHT_BLUE);
        CH::addText("all\n");
    }

    /**
     * Perform the project addition stage.
     *
     * This method prompts the user to paste the Laravel project path, reads the input,
     * and calls the UserProjectsService to add a new project or update an existing one.
     * @param string $laravelRootDirectory The optional Laravel project path.
     * @return string|false Returns true if the project addition or update is successful, false otherwise.
     */
    private function projectAdditionStage(string $laravelRootDirectory = "")
    {
        if ($laravelRootDirectory == "") {
            CH::addText(">> Paste the Laravel project path:\n", Color::GREEN);
            CH::apply();

            $laravelRootDirectory = readline();
        }

        $result = $this->userProjectsService->addNewProjectOrUpdate($laravelRootDirectory);

        if ($result != false) {
            $this->postmanAdditionStage($result);
        }
        return $result;
    }

    /**
     * Perform the Postman information (Collection-ID, Api-Key) addition stage for a given project.
     *
     * @param string $projectName The name of the project.
     * 
     * @return void
     */
    private function postmanAdditionStage(string $projectName): void
    {
        $status = true;

        while (true) {
            if (!$status) {
                CH::addText("\n<Invalid Collection-ID or Api-Key>\n", Color::RED);
                $status = true;
            }

            $collectionId = $this->getCollectionId();
            if ($collectionId == '$') {
                break;
            }

            $apiKey = $this->getApiKey();

            $result = $this->userProjectsService->updatePostmanInformation($projectName, $collectionId, $apiKey);

            if ($result) {
                break;
            }

            $status = $result;
        }
    }

    /**
     * Get the Postman Collection ID from the user.
     *
     * @return string The Postman Collection ID or '$' to skip.
     */
    private function getCollectionId(): string
    {
        $status = true;

        while (true) {
            if (!$status) {
                CH::addText("\n<Invalid Collection-ID>\n", Color::RED);
            }
            CH::addText(">> Enter (Postman Collection-ID) / ($) to skip this step:\n", Color::GREEN);
            CH::apply();
            $collectionId = readLine();

            if ($collectionId == '$') {
                return '$';
            }

            if ($collectionId != false) {
                return $collectionId;
            }

            $status = false;
        }
    }

    /**
     * Get the Postman API Key from the user.
     *
     * @return string The Postman API Key.
     */
    private function getApiKey(): string
    {
        $status = true;

        while (true) {
            if (!$status) {
                CH::addText("\n<Invalid Api-Key>\n", Color::RED);
            }

            CH::addText(">> Enter (Postman Api-Key):\n", Color::GREEN);
            CH::apply();
            $apiKey = readLine();

            if ($apiKey != false) {
                return $apiKey;
            }

            $status = false;
        }
    }

    /**
     * Update the request body type for a specific project stage.
     *
     * @param string $projectName The name of the project.
     * @return void
     */
    private function updateRequestBodyTypeStage(string $projectName)
    {
        $typeMap = [
            'r' => 'RAW',
            'u' => 'URLENCODED',
            'f' => 'FORM_DATA',
        ];

        $requestsBodyType = $this->userProjectsService->userProjects[$projectName]['request'];

        $storeBodyType = $this->getBodyType('Store', $requestsBodyType['store'], $typeMap);

        $bulkStoreBodyType = $this->getBodyType('Bulk Store', $requestsBodyType['bulk_store'], $typeMap);

        $requestsBodyType = [
            'store' => $storeBodyType,
            'bulk_store' => $bulkStoreBodyType
        ];

        $this->userProjectsService->updateRequestBodyType($projectName, $requestsBodyType);
    }

    /**
     * Get the body type for a specific API.
     *
     * @param string $apiName The name of the API.
     * @param string $currentType The current body type of the API.
     * @param array $typeMap The mapping of input keys to body types.
     * @return string The selected body type for the API.
     */
    private function getBodyType(string $apiName, string $currentType, array $typeMap): string
    {
        $status = true;
        while (true) {
            if (!$status) {
                CH::addText("\n<Invalid INPUT>\n", Color::RED);
            }
            CH::addText("\nAvailable body types are: Raw JSON (r), Formdata (f), and URL encoded (u)\n", Color::RED);
            CH::addText(">> The current body type for ($apiName API) is $currentType. select from available types or $ to skip:\n", Color::GREEN);
            CH::apply();
            $newType = readLine();

            if ($newType == "$") {
                return $currentType;
            }

            if (in_array($newType, ['r', 'f', 'u'])) {
                return $typeMap[$newType];
            }

            $status = false;
        }
    }
}
