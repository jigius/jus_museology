<?php

namespace Jus\Core;

use Jus\Core\Field\PlainFld;
use DomainException;
use Exception;

final class InvalidFieldException extends DomainException implements FieldContainerInterface
{
	/**
	 * @var FieldInterface|PlainFld
	 */
	private $field;

	/**
	 * @param FieldInterface $field
	 * @param string $message
	 * @param int $code
	 * @param Exception $previous
	 */
	public function __construct($field, $message = "", $code = 0, $previous = null)
	{
		parent::__construct($message, $code, $previous);
		$this->field = $field;
	}

	/**
	 * @inheritDoc
	 */
	public function field()
	{
		return $this->field;
	}
}
