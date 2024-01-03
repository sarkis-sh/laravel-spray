<?php

declare(strict_types=1);

namespace src\Models\Postman;


/**
 * Represents a group of items in a Postman collection.
 *
 * This class holds information such as the group's name and an array of associated items.
 *
 * @package src\Models\Postman
 */

class ItemGroup
{
    /** @var string $name The name of the item. */
    public string $name;

    /** @var Item[] $item An array of Item objects. */
    public array $item;


    /**
     * @param string $name The name of the item.
     * @param Item[]|null $items
     */
    public function __construct(
        string $name,
        array $item
    ) {
        $this->name = $name;
        $this->item = $item;
    }
}
