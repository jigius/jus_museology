<?php
namespace Jus\Core;

use Jus\Foundation as F;

final class Fields implements F\FieldsInterface
{
	/**
	 * @var array
	 */
	private $coll;

	/**
	 * @var F\AttributesInterface
	 */
	private $attrs;

	/**
	 * Cntr
	 */
	public function __construct()
	{
		$this->coll = [];
		$this->attrs = new Attributes();
	}

	/**
	 * @inheritDoc
	 */
	public function attrs()
	{
		return $this->attrs;
	}

	/**
	 * @inheritDoc
	 */
	public function with(F\FieldInterface $f, $id = "")
	{
		$that = $this->blueprinted();
		$that->coll[] = [$id, $f];
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function without($id)
	{
		$that = $this->blueprinted();
		$that->coll =
			array_filter(
				$this->coll,
				function ($itm) use ($id) {
					return $itm[0] != $id;
				}
			);
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function each($callee)
	{
		foreach ($this->coll as $itm) {
			if (call_user_func($callee, $itm[1], $itm[0]) === false) {
				break;
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function withAttrs(F\AttributesInterface $a)
	{
		$that = $this->blueprinted();
		$that->attrs = $a;
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function printed(F\PrinterInterface $printer)
	{
		$printer =
			$printer
				->with('attrs', $this->attrs);
		$this
			->each(function ($field) use (&$printer) {
				$printer = $field->printed($printer);
			});
		return $printer;
	}

	/**
	 * Clones the instance
	 * @return Fields
	 */
	public function blueprinted()
	{
		$that = new self();
		$that->attrs = $this->attrs;
		$that->coll = $this->coll;
		return $that;
	}
}