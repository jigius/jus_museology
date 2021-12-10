<?php

use Jus\Core\Message\MessagePrn;
use Jus\Core\MessageInterface;
use Jus\Foundation\PrinterInterface;

/**
 * Printer class
 * Prints out Message instance "into session". Data belonging to a session is mutating.
 * Returns none
 */
class SessionPrn implements PrinterInterface
{
	/**
	 * @var Session
	 */
	private $s;
	/**
	 * @var array
	 */
	private $i;

	public function __construct(Session $s)
	{
		$this->s = $s;
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
		if (
			!isset($this->i['message']) ||
			!$this->i['message'] instanceof MessageInterface
		) {
			throw new LogicException("type invalid");
		}
		$d = [
			MessageInterface::TYPE_ERROR => [],
			MessageInterface::TYPE_WARNING => [],
			MessageInterface::TYPE_SUCCESS => []
		];
		foreach (array_keys($d) as $type) {
			if (isset($this->i[$type]) && is_array($this->i[$type])) {
				$d[$type] = $this->i[$type];
			}
		}
		$this->s->data[MessageInterface::CONTAINER_NAME] = $d;
	}

	/**
	 * Clones the instance
	 * @return $this
	 */
	public function blueprinted()
	{
		$that = new self($this->s, $this->b);
		$that->i = $this->i;
		return $that;
	}
}
