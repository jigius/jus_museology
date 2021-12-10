<?php
namespace Jus\Core;

use Jus\Foundation\PrinterInterface;

require_once __DIR__ . "/../Foundation/PrinterInterface.php";
require_once __DIR__ . "/UrlInterface.php";

/*
 * Url Printer
 */
final class UrlPrn implements PrinterInterface
{
	/**
	 * @var UrlInterface
	 */
	private $blank;

	/**
	 * @var array
	 */
	private $i;

	/**
	 * @param UrlInterface $blank
	 */
	public function __construct($blank)
	{
		$this->blank = $blank;
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
	 * @return UrlInterface
	 */
	public function finished()
	{
		if (!isset($this->i['params']) || !is_array($this->i['params'])) {
			throw new \LogicException("type invalid");
		}
		if (!isset($this->i['request']) || !is_a($this->i['request'], \Request::class)) {
			throw new \LogicException("type invalid");
		}
		$url = $this->blank;
		foreach ($this->i['params'] as $name => $val) {
			if (!is_string($name)) {
				throw new \LogicException("type invalid");
			}
			if (isset($this->i['request']->get[$name])) {
				/* fetches a param from request's get-params */
				$val = $this->i['request']->get[$name];
			} elseif (!isset($val)) {
				continue;
			}
			$url = $url->withParam($name, $val);
		}
		return $url;
	}

	/**
	 * Clones the instance
	 * @return $this
	 */
	public function blueprinted()
	{
		$that = new self($this->blank);
		$that->i = $this->i;
		return $that;
	}
}
