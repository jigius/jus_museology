<?php

namespace Jus\Foundation;

interface FieldInterface extends MediaInterface, AttributableInterface
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
	public function withAttrs(AttributesInterface $a);
}
