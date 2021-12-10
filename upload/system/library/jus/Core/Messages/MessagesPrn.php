<?php
namespace Jus\Core\Message;

use Jus\Core\MessageInterface;
use Jus\Foundation\PrinterInterface;
use Session;
use LogicException;

/**
 * Printer class
 * Prints out Session into Message instance.
 * Returns Message instance with data restored from data, early stored into a session.
 */
class MessagePrn implements PrinterInterface
{
	/**
	 * @var array
	 */
	private $i;
	/**
	 * @var MessageInterface
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
	 * @returns MessageInterface
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
				MessageInterface::TYPE_SUCCESS
			];
			foreach ($known as $type) {
				if (
					isset($this->i['session']->data[MessageInterface::CONTAINER_NAME][$type]) &&
					is_array($this->i['session']->data[MessageInterface::CONTAINER_NAME][$type])
				) {
					foreach ($this->i['session']->data[MessageInterface::CONTAINER_NAME][$type] as $txt) {
						$m = $m->with($type, $txt);
					}
				}
			}
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
