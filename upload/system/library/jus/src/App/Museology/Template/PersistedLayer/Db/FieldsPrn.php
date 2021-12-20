<?php

namespace Jus\App\Museology\Template\PersistedLayer\Db;

use Jus\App\Museology\Template\PersistedLayer\Db\Field as Field;
use Jus\Core as C;
use Jus\Foundation as F;
use Model;
use LogicException;

/**
 * Printer class.
 * Prints out fields instance that is contains fields from persisted layer DB for specified category's id
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

	public function with($key, $val)
	{
		$that = $this->blueprinted();
		$that->i[$key] = $val;
		return $that;
	}

	public function finished()
	{
		if (!isset($this->i['model'])) {
			throw new LogicException("mandatory param is not defined");
		}
		if (!is_a($this->i['model'], Model::class)) {
			throw new LogicException("type invalid");
		}
		if (!method_exists($this->i['model'], 'template')) {
			throw new LogicException("type invalid");
		}
		if (!isset($this->i['categoryId'])) {
			throw new LogicException("mandatory param is not defined");
		}
		if (!is_integer($this->i['categoryId'])) {
			throw new LogicException("type invalid");
		}
		$fields = $this->f;
		foreach ($this->i['model']->template($this->i['categoryId']) as $row) {
			$attrs =
				(new C\Attributes())
					->with('language_id', $row['language_id']);
			$fields =
				$fields
					->with(
						(new Field\MetaTitleFld())
							->withName('meta_title')
							->withValue($row['meta_title'])
							->withAttrs(
								$attrs
									->with('info', 'text_fld_meta_title_info')
							)
					)
					->with(
						(new Field\MetaDescriptionFld())
							->withName('meta_description')
							->withValue($row['meta_description'])
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
	 * @return FieldsPrn
	 */
	public function blueprinted()
	{
		$that = new self($this->f);
		$that->i = $this->i;
		return $that;
	}
}
