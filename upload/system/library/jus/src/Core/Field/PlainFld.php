<?php
namespace Jus\Core\Field;

use Jus\Core\Attributes;
use Jus\Foundation as F;

/**
 * Implements trivial field for using with form
 */
final class PlainFld implements PlainInterface
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
	public function printed(F\PrinterInterface $printer)
	{
		return
			$printer
				->with('field', $this->i)
				->finished();
	}

	/**
	 * @inheritDoc
	 */
	public function withAttrs(F\AttributesInterface $a)
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
