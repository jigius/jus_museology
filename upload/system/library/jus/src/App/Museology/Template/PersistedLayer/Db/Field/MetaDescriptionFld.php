<?php
namespace Jus\App\Museology\Template\PersistedLayer\Db\Field;

use Jus\Core\Field AS Fld;
use Jus\Core\Illuminate\ArrayMedia;
use Jus\Core\Illuminate\Printer\ArrayPrn;
use Jus\Core as C;
use Jus\Foundation as F;
use LogicException;

/**
 * Class MetaDescriptionFld
 */
final class MetaDescriptionFld implements Fld\PlainFieldInterface
{
	const CONSTRAINT_MAX_LENGTH = 255;
	/**
	 * @var Fld\PlainFieldInterface
	 */
	private $original;

	/**
	 * Cntr
	 * @param Fld\PlainFieldInterface|null $f
	 */
	public function __construct(Fld\PlainFieldInterface $f = null)
	{
		$this->original = isset($f)? $f: new Fld\PlainFld();
	}

	/**
	 * @inheritDoc
	 */
	public function attrs()
	{
		return $this->original->attrs();
	}

	/**
	 * @inheritDoc
	 */
	public function withName($name)
	{
		$that = $this->blueprinted();
		$that->original = $this->original->withName($name);
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function withValue($val)
	{
		$that = $this->blueprinted();
		$that->original = $this->original->withValue($val);
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function withAttrs(F\AttributesInterface $a)
	{
		$that = $this->blueprinted();
		$that->original = $this->original->withAttrs($a);
		return $that;
	}

	/**
	 * @inheritDoc
	 * @throw LogicException
	 */
	public function unserialized($data)
	{
		if (
			!isset($data['original']['classname']) ||
			!class_exists($data['original']['classname']) ||
			!isset($data['original']['state']) ||
			!is_array($data['original']['state'])
		) {
			throw new LogicException("data is corrupted");
		}
		$that = $this->blueprinted();
		$that
			->original =
				(new $data['original']['classname']())
					->unserialized($data['original']['state']);
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function serialized()
	{
		return [
			'original' => [
				'classname' => get_class($this->original),
				'state' => $this->original->serialized()
			]
		];
	}

	/**
	 * @inheritDoc
	 * @throw InvalidFieldException
	 */
	public function printed(F\PrinterInterface $printer)
	{
		$i = $this->original->printed(new ArrayPrn());
		if (isset($i['value']) && strlen($i['value'] > self::CONSTRAINT_MAX_LENGTH)) {
			throw
				new C\InvalidFieldException(
					$this
						->withAttrs(
							$this
								->attrs()
								->with('error_cause', 'error_max_length_is_violated')
						)
				);
		}
		return (new ArrayMedia($i))->printed($printer);
	}

	/**
	 * Clones the instance
	 * @return MetaDescriptionFld
	 */
	public function blueprinted()
	{
		return new self($this->original);
	}
}
