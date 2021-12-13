<?php
namespace Jus\Core;

final class Url implements UrlInterface
{
	/**
	 * @var array
	 */
	private $i;

	public function __construct() {
		$this->i = [
			'path' => "",
			'params' => []
		];
	}

	/**
	 * @inheritDoc
	 */
	public function url(\Url $url)
	{
		return $url->link($this->i['path'], $this->i['params'], true);
	}

	/**
	 * @inheritDoc
	 */
	public function withParam($name, $value)
	{
		$that = $this->blueprinted();
		$that->i['params'][$name] = $value;
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function withoutParam($name)
	{
		if (!isset($this->i['params'][$name])) {
			return $this;
		}
		$that = $this->blueprinted();
		unset($that->i['params'][$name]);
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function withPath($path)
	{
		$that = $this->blueprinted();
		$that->i['path'] = $path;
		return $that;
	}

	/**
	 * @return $this
	 */
	public function blueprinted() {
		$that = new self();
		$that->i = $this->i;
		return $that;
	}
}
