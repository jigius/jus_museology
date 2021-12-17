<?php

namespace Jus\Foundation;

/**
 * Trivial collection of attributes
 */
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
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function fetch($name, $default = null);

	/**
	 * @param string $name
	 * @return bool
	 */
	public function has($name);

	/**
	 * @param callable $callee
	 * @return void
	 */
	public function each($callee);
}
