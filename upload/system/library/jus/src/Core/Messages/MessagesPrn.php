<?php
namespace Jus\Core\Messages;

use Jus\Core\MessagesInterface;
use Jus\Core\MessageInterface;
use Jus\Foundation\PrinterInterface;
use LogicException;
use Session;

/**
 * Printer class
 * Prints out Session into Message instance.
 * Returns `Messages` instance with data restored from data, early stored into a session.
 */
class MessagesPrn implements PrinterInterface
{
	/**
	 * @var array
	 */
	private $i;
	/**
	 * @var MessagesInterface
	 */
	private $b;

	public function __construct($blank)
	{
		$this->i = [];
		$this->b = $blank;
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
	 * @returns MessagesInterface
	 * @throws LogicException
	 */
	public function finished()
	{
		if (!isset($this->i['session']) || is_a(!$this->i['session'], Session::class)) {
			throw new LogicException("type invalid");
		}
		$m = $this->b;
		if (
			isset($this->i['session']->data[MessageInterface::CONTAINER_NAME]) &&
			is_array($this->i['session']->data[MessageInterface::CONTAINER_NAME])
		) {
			$known = [
				MessageInterface::TYPE_SUCCESS,
				MessageInterface::TYPE_WARNING,
				MessageInterface::TYPE_ERROR
			];
			foreach ($known as $type) {
				if (!empty($this->i['session']->data[MessageInterface::CONTAINER_NAME][$type])) {
					$m = $m->with($type, $this->i['session']->data[MessageInterface::CONTAINER_NAME][$type]);
				}
			}
			unset($this->i['session']->data[MessageInterface::CONTAINER_NAME]);
		}
		return $m;
	}

	/**
	 * Clones the instance
	 * @return $this
	 */
	public function blueprinted()
	{
		$that = new self($this->b);
		$that->i = $this->i;
		return $that;
	}
}
