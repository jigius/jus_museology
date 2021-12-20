<?php

namespace Jus\App\Museology\Template\Request;

use Jus\App\Museology\Template\PersistedLayer\Db\Field as Field;
use Jus\Core as C;
use Jus\Foundation as F;
use Request;
use LogicException;

/**
 * Printer class.
 * Prints out fields instance that is contains form's fields from a POST-request
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
		if (!isset($this->i['request']) || !is_a($this->i['request'], Request::class)) {
			throw new LogicException("mandatory param is not defined");
		}
		if (!isset($this->i['request']->post['tpl'])) {
			throw new LogicException("mandatory param is not defined");
		}
		if (!is_array($this->i['request']->post['tpl'])) {
			throw new LogicException("type invalid");
		}
		$fields = $this->f;
		foreach ($this->i['request']->post['tpl'] as $languageId => $value) {
			if (!is_numeric($languageId)) {
				throw new LogicException("invalid type");
			}
			if (!isset($value['meta_title']) || !is_string($value['meta_title'])) {
				throw new LogicException("invalid type");
			}
			if (!isset($value['meta_description']) || !is_string($value['meta_description'])) {
				throw new LogicException("invalid type");
			}
			$attrs =
				(new C\Attributes())
					->with('language_id', $languageId);
			$fields =
				$fields
					->with(
						(new Field\MetaTitleFld())
							->withName('meta_title')
							->withValue($value['meta_title'])
							->withAttrs(
								$attrs
									->with('info', 'text_fld_meta_title_info')
							)
					)
					->with(
						(new Field\MetaDescriptionFld())
							->withName('meta_description')
							->withValue($value['meta_description'])
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
