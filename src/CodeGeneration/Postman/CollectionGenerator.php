<?php

declare(strict_types=1);

namespace src\CodeGeneration\Postman;

use src\CodeGeneration\AbstractCodeGenerator;
use src\CodeGeneration\Postman\Request\RequestFactory;
use src\Config\Paths\OutPaths;
use src\Helpers\NamingHelper;
use src\Models\Postman\Item;
use src\Models\Postman\ItemGroup;
use src\Services\Http\PostmanService;
use src\Services\UserProjectsService;
use src\Shared\CodeGenerationContext as CGC;
use src\Utils\FileManager;

/**
 * Class CollectionGenerator
 *
 * This class is responsible for generating a Postman collection based on the selected APIs and project details.
 * It implements the CodeGeneratorInterface and provides a method to generate the collection.
 * 
 * @package src\CodeGeneration\Postman
 */
class CollectionGenerator extends AbstractCodeGenerator
{
    /** @var string The name of the current folder. */
    private string $currentFolderName;

    /** @var string The name of the project. */
    private string $projectName;

    /** @var string The path to the generated collection file. */
    private string $collectionPath;

    /** @var PostmanService Provides methods for interacting with Postman.. */
    private PostmanService $postmanService;

    /** @var UserProjectsService perform various functionalities related to user projects. */
    private UserProjectsService $userProjectsService;

    public function __construct(UserProjectsService $userProjectsService, PostmanService $postmanService)
    {
        $this->userProjectsService = $userProjectsService;
        $this->postmanService = $postmanService;
        $this->projectName = CGC::$projectName;
        $this->collectionPath = OutPaths::POSTMAN_OUT_DIRECTORY . $this->projectName . "\\$this->projectName.postman_collection.json";
        FileManager::makeDirectory(dirname($this->collectionPath));
    }

    /**
     * Generate the Postman collection.
     *
     * Generate and sync Postman collection based on selected tables and APIs
     */
    public function generate(array $params = [])
    {
        $postmanInfo = $this->userProjectsService->getPostmanInfo(CGC::$projectName);

        if ($postmanInfo != null) {

            $collectionId = $postmanInfo['collection_id'];
            $apiKey = $postmanInfo['x-api-key'];

            $response = $this->postmanService->getCollection($collectionId, $apiKey);
            $collection = $response->body;

            foreach (CGC::$selectedTables as $selectedTable) {

                $this->currentFolderName = NamingHelper::snakeCaseToTitleCase($selectedTable->tableName);

                $items = [];
                foreach (CGC::$selectedAPIs as $api) {
                    $request = RequestFactory::create($selectedTable, $api['name'], $this->userProjectsService->userProjects[CGC::$projectName]);
                    $items[$api['option_name']] = $request->generate();
                }

                $itemGroup = null;
                $itemGroupIndex = null;
                foreach ($collection['collection']['item'] as $index => $item) {
                    if ($item['name'] == $this->currentFolderName) {
                        $itemGroup = $item;
                        $itemGroupIndex = $index;
                        break;
                    }
                }

                if ($itemGroup != null) {
                    $newApis = [];
                    foreach ($itemGroup['item'] as $index => $api) {
                        if (isset($items[$api['name']])) {
                            /** @var Item */
                            $matchingItem = $items[$api['name']];
                            if ($matchingItem->request->body != null)
                                $itemGroup['item'][$index]['request']['body'] = $matchingItem->request->body;
                            if ($matchingItem->request->url->query != null) {
                                $itemGroup['item'][$index]['request']['url']['query'] = $matchingItem->request->url->query;
                                $itemGroup['item'][$index]['request']['url']['raw'] = $matchingItem->request->url->raw;
                            }
                            unset($items[$api['name']]);
                        }
                    }

                    $newApis = array_merge($newApis, array_values($items));

                    $itemGroup['item'] = array_merge($itemGroup['item'], array_values($newApis));
                    $collection['collection']['item'][$itemGroupIndex] = $itemGroup;
                } else {
                    $itemGroup = new ItemGroup($this->currentFolderName, array_values($items));
                    $collection['collection']['item'][] = (array) $itemGroup;
                }
            }

            $encodedCollection = json_encode($collection, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

            $result = file_put_contents($this->collectionPath, $encodedCollection);

            $response = $this->postmanService->updateCollection($collectionId,  $apiKey, $encodedCollection);

            parent::log("Postman collection [" . CGC::$projectName . "]", ($result && ($response->status == 'Success')), 'updated');
        }
    }
}
