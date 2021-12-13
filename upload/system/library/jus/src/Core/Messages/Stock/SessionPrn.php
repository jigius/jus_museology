<?php

namespace Jus\Core\Messages\Stock;

use Jus\Core\MessageInterface;
use Jus\Core\MessagesInterface;
use Jus\Foundation\PrinterInterface;
use Session;
use LogicException;

/**
 * Printer class
 * Prints out Message instance "into stock oc3 session". Data belonging to a session is mutating.
 * Returns none
 */
class SessionPrn implements PrinterInterface
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
		if (!isset($this->i['session']) || is_a(!$this->i['session'], Session::class)) {
			throw new LogicException("invalid type");
		}
		$m = [
			MessageInterface::TYPE_SUCCESS => 'success',
			MessageInterface::TYPE_WARNING => 'error',
			MessageInterface::TYPE_ERROR => 'error'
		];
		foreach ($m as $type => $stype) {
			if (!empty($this->i[$type])) {
				/* pushes a message in session data */
				$this->i['session']->data[$stype] = $this->i[$type];
			}
		}
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
