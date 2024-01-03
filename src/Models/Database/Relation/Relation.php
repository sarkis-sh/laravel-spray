<?php

declare(strict_types=1);

namespace src\Models\Database\Relation;

use src\Constants\NameFormatType;
use src\Helpers\NamingHelper;


/**
 * Abstract class for representing database relations.
 *
 * This class serves as a base for specific database relation types and provides common functionality
 * for setting and retrieving the relation name.
 *
 * @package src\Models\Database\Relation
 */
abstract class Relation
{
    /** @var string The name of the relation. */
    protected string $relationName;

    /**
     * Sets the relation name based on the referenced table name.
     *
     * @param string $referencedTableName The name of the referenced table.
     * @return void
     */
    protected function setRelationName($referencedTableName): void
    {
        $this->relationName = NamingHelper::getVarName($referencedTableName, NameFormatType::SINGULAR);
    }

    /**
     * Gets the name of the relation.
     *
     * @return string The name of the relation.
     */
    public function getRelationName(): string
    {
        return $this->relationName;
    }
    /**
     * Converts the relation to its string representation.
     *
     * This method must be implemented by concrete subclasses.
     *
     * @return string The string representation of the relation.
     */
    public abstract function __toString();
}
