<?php

namespace Jus\Core;

use Jus\Foundation\AttributesInterface;
use LogicException;

/**
 * Implements trivial collection of attributes
 */
final class Attributes implements AttributesInterface
{
	/**
	 * @var array
	 */
	private $coll;

	/**
	 * Cntr
	 */
	public function __construct()
	{
		$this->coll = [];
	}

	/**
	 * @inheritDoc
	 */
	public function with($name, $val)
	{
		$that = $this->blueprinted();
		$that->coll[$name] = $val;
		return $that;
	}

	/**
	 * @inheritDoc
	 * @throw LogicException
	 */
	public function withOut($name)
	{
		if (!$this->has($name)) {
			throw new LogicException("attr with name=`$name` is unknown");
		}
		return $this->coll[$name];
	}

	/**
	 * @inheritDoc
	 */
	public function each($callee)
	{
		foreach ($this->coll as $key => $val) {
			if (call_user_func($callee, $val, $key) === false) {
				break;
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function fetch($name, $default = null)
	{
		return $this->has($name)? $this->coll[$name]: $default;
	}

	/**
	 * @inheritdoc
	 */
	public function has($name)
	{
		return array_key_exists($name, $this->coll);
	}

	/**
	 * Clones the instance
	 * @return Attributes
	 */
	public function blueprinted()
	{
		$that = new self();
		$that->coll = $this->coll;
		return $that;
	}
}
