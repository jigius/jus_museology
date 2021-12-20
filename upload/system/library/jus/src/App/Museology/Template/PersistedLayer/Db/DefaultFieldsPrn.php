<?php

namespace Jus\App\Museology\Template\PersistedLayer\Db;

use Jus\App\Museology\Template\PersistedLayer\Db\Field as Field;
use Jus\Core as C;
use Jus\Foundation as F;
use LogicException;

/**
 * Printer class.
 * Prints out fields instance that is contains fields with its default values for specified language's ids
 */
final class DefaultFieldsPrn implements F\PrinterInterface
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

	public function with($key, $val)
	{
		$that = $this->blueprinted();
		$that->i[$key] = $val;
		return $that;
	}

	public function finished()
	{
		if (!isset($this->i['languageIds'])) {
			throw new LogicException("mandatory param is not defined");
		}
		if (!is_array($this->i['categoryId'])) {
			throw new LogicException("type invalid");
		}
		if (empty($this->i['languageIds'])) {
			throw new LogicException("empty set");
		}
		$fields = $this->f;
		foreach ($this->i['languageId'] as $id) {
			$attrs =
				(new C\Attributes())
					->with('language_id', $id);
			$fields =
				$fields
					->with(
						(new Field\MetaTitleFld())
							->withName('meta_title')
							->withValue("")
							->withAttrs(
								$attrs
									->with('info', 'text_fld_meta_title_info')
							)
					)
					->with(
						(new Field\MetaDescriptionFld())
							->withName('meta_description')
							->withValue("")
							->withAttrs(
								$attrs
									->with('info', 'text_fld_meta_description_info')
							)
					);
		}
		return $fields;
	}

	/**
	 * Clones the instance
	 * @return DefaultFieldsPrn
	 */
	public function blueprinted()
	{
		$that = new self($this->f);
		$that->i = $this->i;
		return $that;
	}
}
