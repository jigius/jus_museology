<?php

namespace Jus\Core\Field;

use Jus\Foundation\AttributesInterface;
use Jus\Foundation\PrinterInterface;

class PlainFld implements PlainInterface
{
	/**
	 * @var array
	 */
	private $i;

	public function __construct()
	{
		$this->i = [
			'attrs' => new Attributes()
		];
	}

	/**
	 * @inheritDoc
	 */
	public function withName($name)
	{
		$that = $this->blueprinted();
		$that->i['name'] = $name;
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function withValue($val)
	{
		$that = $this->blueprinted();
		$that->i['val'] = $val;
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function printed(PrinterInterface $printer)
	{
		/*$this
			->i['attrs']
			->each*/
	}

	/**
	 * @inheritDoc
	 */
	public function withAttrs(AttributesInterface $a)
	{
		$that = $this->blueprinted();
		$that->i['attrs'] = $a;
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function attrs()
	{
		return $this->attrs();
	}

	/**
	 * Clones the instance
	 * @return $this
	 */
	public function blueprinted()
	{
		$that = new self();
		$that->i = $this->i;
		return $that;
	}
}