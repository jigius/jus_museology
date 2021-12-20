<?php

namespace Jus\Core;

use Jus\Foundation as F;

interface FieldInterface extends F\MediaInterface, F\AttributableInterface, F\SerializableInterface
{
	/**
	 * @param string $name
	 * @return FieldInterface
	 */
	public function withName($name);

	/**
	 * @inheritDoc
	 * @return FieldInterface
	 */
	public function withAttrs(F\AttributesInterface $a);

	/**
	 * @inheritDoc
	 * @return FieldInterface
	 */
	public function unserialized($data);
}
