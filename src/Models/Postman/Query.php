<?php

declare(strict_types=1);

namespace src\Models\Postman;

/**
 * Represents a query parameter in a Postman request.
 *
 * This class holds information such as the parameter's key, value, and description.
 *
 * @package src\Models\Postman
 */
class Query
{
	/** @var string $key The key of the row. */
	public string $key;

	/** @var string $value The value of the row. */
	public string $value;

	/** @var string $description The description of the row. */
	public string $description;


	/**
	 * @param string $key The key of the row.
	 * @param string $value The value of the row.
	 * @param string $description The description of the row.
	 */
	public function __construct(
		string $key,
		string $value,
		string $description
	) {
		$this->key = $key;
		$this->value = $value;
		$this->description = $description;
	}
}
