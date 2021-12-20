<?php

namespace Jus\Core;

use Jus\Foundation as F;

/**
 * Trivial collection of field
 */
interface FieldsInterface extends F\MediaInterface, F\AttributableInterface, F\SerializableInterface
{
	/**
	 * Adds a field
	 * @param FieldInterface $f
	 * @param string $id Optional ID for the field
	 * @return FieldsInterface
	 */
	public function with(FieldInterface $f, $id = "");

	/**
	 * @param string $id
	 * @return FieldsInterface
	 */
	public function without($id);

	/**
	 * @param callable $callee
	 * @return void
	 */
	public function each($callee);

	/**
	 * @inheritDoc
	 * @return F\AttributableInterface
	 */
	public function withAttrs(F\AttributesInterface $a);

	/**
	 * @inheritDoc
	 * @return FieldsInterface
	 */
	public function unserialized($data);
}
