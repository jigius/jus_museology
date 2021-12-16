<?php

namespace Jus\Core\Field;

use Jus\Foundation\FieldInterface;

interface PlainInterface extends FieldInterface
{
	/**
	 * @param $val
	 * @return PlainInterface
	 */
	public function withValue($val);
}
