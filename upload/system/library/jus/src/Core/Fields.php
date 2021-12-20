<?php
namespace Jus\Core;

use Jus\Foundation as F;
use LogicException;

final class Fields implements FieldsInterface
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
	public function with(FieldInterface $f, $id = "")
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
	 * @throw LogicException
	 */
	public function unserialized($data)
	{
		if (
			!isset($data['attrs']['classname']) ||
			!class_exists($data['attrs']['classname']) ||
			!isset($data['attrs']['state']) ||
			!is_array($data['attrs']['state'])
		) {
			throw new LogicException("data is corrupted");
		}
		$that =
			$this
				->blueprinted()
				->withAttrs(
					(new $data['classname']())
						->unserialized($data['state'])
				);
		if (!isset($data['coll']) || !is_array($data['coll'])) {
			throw new LogicException("data is corrupted");
		}
		$that->coll = [];
		foreach ($data['coll'] as $f) {
			if (!isset($f['classname']) || !class_exists($f['classname'])) {
				throw new LogicException("data is corrupted");
			}
			if (!isset($f['state']) || !is_array($f['state'])) {
				throw new LogicException("data is corrupted");
			}
			if (!isset($f['id']) || !is_array($f['id'])) {
				throw new LogicException("data is corrupted");
			}
			$that =
				$that
					->with(
						(new $f['classname']())
							->unserialized($f['state']),
						$f['id']
					);
		}
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function serialized()
	{
		$coll = [];
		$this
			->each(function ($field, $id) use (&$coll) {
				$coll[] = [
					'classname' => get_class($field),
					    'state' => $field->serialized(),
					       'id' => $id
					];
			});
		return [
			'attrs' => [
				'classname' => get_class($this->attrs),
				'state' => $this->attrs->serialized()
			],
			'coll' => $coll
		];
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
