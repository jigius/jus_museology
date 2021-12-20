<?php
namespace Jus\Core\Field;

use Jus\Core\Attributes;
use Jus\Foundation as F;
use LogicException;

/**
 * Implements trivial field for using with form
 */
final class PlainFld implements PlainFieldInterface
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
					(new $this->i['classname']())
						->unserialized($this->i['state'])
				);
		unset($that->i['name']);
		unset($that->i['value']);
		if (isset($data['name'])) {
			if (!is_string($data['name'])) {
				throw new LogicException("data is corrupted");
			}
			$that = $that->withName($this->i['name']);
		}
		if (isset($data['value'])) {
			$that = $that->withValue($this->i['value']);
		}
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function serialized()
	{
		return
			array_filter(
				[
					'attrs' => [
						'classname' => get_class($this->i['attrs']),
						'state' => $this->i['attrs']->serialized()
					],
					'name' => isset($this->i['name'])? $this->i['name']: null,
					'value' => isset($this->i['value'])? $this->i['value']: null,

				]
			);
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
