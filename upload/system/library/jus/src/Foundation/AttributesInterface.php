<?php

namespace Jus\Foundation;

use Iterator;

interface AttributesInterface
{
	/**
	 * @param string $name
	 * @param mixed $val
	 * @return AttributesInterface
	 */
	public function with($name, $val);

	/**
	 * @param string $name
	 * @return AttributesInterface
	 */
	public function withOut($name);

	/**
	 * @return Iterator
	 */
	public function iterator();
}
