<?php

namespace Jus\Core\Messages;

use Jus\Core\MessageInterface;
use Jus\Core\MessagesInterface;
use Jus\Foundation\PrinterInterface;
use Session;
use LogicException;

/**
 * Printer class
 * Prints out Message instance "into session". Data belonging to a session is mutating.
 * Returns none
 */
class SessionPrn implements PrinterInterface
{
	/**
	 * @var array
	 */
	private $i;

	public function __construct()
	{
		$this->i = [
			'containerName' => MessageInterface::CONTAINER_NAME
		];
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
		if (!is_string($this->i['containerName'])) {
			throw new LogicException("invalid type");
		}
		$d = [
			MessageInterface::TYPE_SUCCESS => null,
			MessageInterface::TYPE_WARNING => null,
			MessageInterface::TYPE_ERROR => null
		];
		foreach (array_keys($d) as $type) {
			if (!empty($this->i[$type])) {
				$d[$type] = $this->i[$type];
			}
		}
		$this->i['session']->data[MessageInterface::CONTAINER_NAME] = array_filter($d);
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
