<?php

declare(strict_types=1);

namespace src\Models\Database\Relation;


use src\Config\Paths\StubPaths;
use src\Constants\NameFormatType;
use src\Helpers\NamingHelper;
use src\Helpers\StubFileProcessor;


/**
 * Represents a "HasMany" database relation.
 *
 * This class extends the abstract "Relation" class and is specifically designed for "HasMany" relationships.
 * It includes methods for generating the string representation of HasMany relation.
 *
 * @package src\Models\Database\Relation
 */
class HasMany extends Relation
{
    /** @var array Array to store variables for stub generation */
    private array $stubVariables;


    /**
     * @param string $localTableName The name of the local table.
     * @param string $referencedTableName The name of the referenced table.
     * @param string $referencedForeignKey The name of the referenced foreign key column.
     * @param string $localKey The name of the local key column.
     */
    public function __construct(
        string $localTableName,
        string $referencedTableName,
        string $referencedForeignKey,
        string $localKey
    ) {
        $this->relationName = NamingHelper::getVarName($referencedTableName, NameFormatType::PLURAL);

        $this->stubVariables = [
            'referencedTableName'    => NamingHelper::getClassName($referencedTableName),
            'localTableName'         => NamingHelper::getVarName($localTableName, NameFormatType::SINGULAR),
            'relationName'           => $this->relationName,
            'referencedForeignKey'   => $referencedForeignKey,
            'localKey'               => $localKey
        ];
    }


    /**
     * Converts the `HasMany` relation to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return StubFileProcessor::replaceVariables(
            StubPaths::HAS_MANY_STUB_PATH,
            $this->stubVariables
        );
    }
}
