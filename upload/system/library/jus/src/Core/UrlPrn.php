<?php
namespace Jus\Core;

use Jus\Foundation\PrinterInterface;
use Request;
use LogicException;

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
		$this->i = [
			'source' => 'request'
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
	 * @return UrlInterface
	 */
	public function finished()
	{
		if (!isset($this->i['params']) || !is_array($this->i['params'])) {
			throw new LogicException("`params` is invalid");
		}
		if (!isset($this->i['request']) || !is_a($this->i['request'], Request::class)) {
			throw new LogicException("`request` is invalid");
		}
		if (!in_array($this->i['source'], ['get', 'post', 'request', 'cookie', 'files', 'server'])) {
			throw new LogicException("`source` is invalid");
		}
		$url = $this->blank;
		foreach ($this->i['params'] as $name => $val) {
			if (!is_string($name)) {
				throw new LogicException("invalid type - `$name` `$val`");
			}
			if (isset($this->i['request']->{$this->i['source']}[$name])) {
				$val = $this->i['request']->{$this->i['source']}[$name];
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
