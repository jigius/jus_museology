<?php

namespace Jus\Core\Field;

use Jus\Core\FieldInterface;

interface PlainFieldInterface extends FieldInterface
{
	/**
	 * @param $val
	 * @return PlainFieldInterface
	 */
	public function withValue($val);

	/**
	 * @inheritDoc
	 * @return FieldInterface
	 */
	public function unserialized($data);
}
