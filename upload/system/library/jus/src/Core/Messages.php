<?php

namespace Jus\Core;

use Jus\Foundation\PrinterInterface;
use LogicException;

/**
 * Class Message
 * Implements an instance for messages are stored in session between requests
 */
final class Messages implements MessagesInterface
{
	/**
	 * @var array
	 */
	private $i;

	/**
	 * Cntr
	 */
	public function __construct()
	{
		$this->i = [];
	}

	/**
	 * @inheritDoc
	 * @throws LogicException
	 */
	public function with($type, $txt)
	{
		if (
			!in_array(
				$type,
				[
					MessageInterface::TYPE_SUCCESS,
					MessageInterface::TYPE_WARNING,
					MessageInterface::TYPE_ERROR
				]
			)
		) {
			throw new LogicException("type invalid");
		}
		$that = $this->blueprinted();
		$that->i[$type] = $txt;
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function cleaned()
	{
		$that = $this->blueprinted();
		$that->i = [];
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function printed(PrinterInterface $printer)
	{
		$p = $printer;
		foreach ($this->i as $k => $v) {
			$p = $p->with($k, $v);
		}
		return $p->finished();
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