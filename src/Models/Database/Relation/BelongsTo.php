<?php

declare(strict_types=1);

namespace src\Models\Database\Relation;


use src\Config\Paths\StubPaths;
use src\Constants\NameFormatType;
use src\Helpers\NamingHelper;
use src\Helpers\StubFileProcessor;


/**
 * Represents a "BelongsTo" database relation.
 *
 * This class extends the abstract "Relation" class and is specifically designed for "BelongsTo" relationships.
 * It includes methods for generating the string representation of BelongsTo relation.
 *
 * @package src\Models\Database\Relation
 */
class BelongsTo extends Relation
{
    /** @var array Array to store variables for stub generation. */
    private array $stubVariables;


    /**
     * @param string $localTableName The name of the local table.
     * @param string $referencedTableName The name of the referenced table.
     * @param string $localForeignKey The name of the local foreign key column.
     * @param string $referencedKey The name of the referenced key column.
     */
    public function __construct(
        string $localTableName,
        string $referencedTableName,
        string $localForeignKey,
        string $referencedKey
    ) {
        $this->setRelationName($referencedTableName);

        $this->stubVariables = [
            'referencedTableName'   => NamingHelper::getClassName($referencedTableName),
            'localTableName'        => NamingHelper::getVarName($localTableName, NameFormatType::SINGULAR),
            'relationName'          => $this->relationName,
            'localForeignKey'       => $localForeignKey,
            'referencedKey'         => $referencedKey
        ];
    }

    /**
     * Converts the `BelongsTo` relation to its string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return StubFileProcessor::replaceVariables(
            StubPaths::BELONGS_TO_STUB_PATH,
            $this->stubVariables
        );
    }
}
