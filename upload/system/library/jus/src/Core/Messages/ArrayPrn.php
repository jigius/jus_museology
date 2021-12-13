<?php

namespace Jus\Core\Messages;

use Jus\Core\MessageInterface;
use Jus\Core\MessagesInterface;
use Jus\Foundation\PrinterInterface;
use LogicException;

/**
 * Printer class
 * Prints out Message instance as an array. The main purpose - for passing it into a template.
 * Returns array
 */
final class ArrayPrn implements PrinterInterface
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
	 */
	public function with($key, $val)
	{
		$that = $this->blueprinted();
		$that->i[$key] = $val;
		return $that;
	}

	/**
	 * @inheritDoc
	 * @returns void
	 */
	public function finished()
	{
		$d = [
			MessageInterface::TYPE_ERROR => "error",
			MessageInterface::TYPE_WARNING => "warning",
			MessageInterface::TYPE_SUCCESS => "success"
		];
		$arr = [];
		foreach ($d as $type => $rType) {
			if (!empty($this->i[$type])) {
				$arr[$rType] = $this->i[$type];
				if ($type === MessageInterface::TYPE_ERROR) {
					break;
				}
			}
		}
		return $arr;
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
