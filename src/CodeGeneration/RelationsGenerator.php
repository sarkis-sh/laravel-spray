<?php

declare(strict_types=1);

namespace src\CodeGeneration;

use src\Helpers\ClassModifier;
use src\Models\Database\Relation\HasMany;
use src\Models\Database\Relation\BelongsTo;

/**
 * Class RelationsGenerator
 *
 * This class is responsible for generating model relations code.
 *
 * @package src\CodeGeneration
 */
class RelationsGenerator  extends AbstractCodeGenerator
{

    /** @var string The model file content. */
    private string $modelFileContent;

    public function __construct(string $modelFileContent)
    {
        $this->modelFileContent = $modelFileContent;
    }

    /**
     * Generate the relations methods for the model.
     *
     * This method generates relation methods for the model based on the defined relationships in the selected table.
     * The generated code is added to the model file if the corresponding relation method does not already exist.
     *
     * @return string The modified model with added relation methods.
     */
    public function generate(array $params = []): string
    {
        // Get the relations from table.
        $relationsObjs = $params['selectedTable']->relations;

        $relations = '';

        $useStatementGenerator = new UseStatementGenerator($this->modelFileContent);

        foreach ($relationsObjs as $relationObj) {

            if (!ClassModifier::functionIsExist($this->modelFileContent, $relationObj->getRelationName())) {
                // If relation function is not exist, then check the type of relation and generate it.
                if ($relationObj instanceof BelongsTo) {
                    $this->modelFileContent = $useStatementGenerator->generate(
                        [
                            'useStatement' => 'Illuminate\Database\Eloquent\Relations\BelongsTo'
                        ]
                    );
                }
                if ($relationObj instanceof HasMany) {
                    $this->modelFileContent = $useStatementGenerator->generate(
                        [
                            'useStatement' => 'Illuminate\Database\Eloquent\Relations\HasMany'
                        ]
                    );
                }
                $relations .= $relationObj;
            }
        }

        return ClassModifier::appendFunction($this->modelFileContent, $relations);
    }
}
