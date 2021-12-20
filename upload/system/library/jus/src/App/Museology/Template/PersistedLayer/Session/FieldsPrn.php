<?php

namespace Jus\App\Museology\Template\PersistedLayer\Session;

use Jus\Core as C;
use Jus\Foundation as F;
use Session;
use LogicException;

/**
 * Printer class.
 * Prints out fields instance that is contains form's fields have serialized into a session early
 */
final class FieldsPrn implements F\PrinterInterface
{
	/**
	 * @var array
	 */
	private $i;
	/**
	 * @var C\FieldsInterface
	 */
	private $f;

	/**
	 * @param C\FieldsInterface $f
	 */
	public function __construct(C\FieldsInterface $f)
	{
		$this->f = $f;
		$this->i = [];
	}

	/**
	 * @inheritDoc
	 */
	public function with($key, $val)
	{
		$that = $this->blueprinted();
		$that->i[$key] = $val;
		return $that;
	}

	/**
	 * @inheritDoc
	 * @throw LogicException
	 * @return C\FieldsInterface
	 */
	public function finished()
	{
		if (!isset($this->i['session']) || !is_a($this->i['session'], Session::class)) {
			throw new LogicException("mandatory param is not defined");
		}
		if (!isset($this->i['session']->data['_preload'])) {
			throw new LogicException("type invalid");
		}
		if (!is_array($this->i['session']->data['_preload'])) {
			throw new LogicException("type invalid");
		}
		return $this->f->unserialized($this->i['session']->data['_preload']);

	}

	/**
	 * Clones the instance
	 * @return FieldsPrn
	 */
	public function blueprinted()
	{
		$that = new self($this->f);
		$that->i = $this->i;
		return $that;
	}
}
