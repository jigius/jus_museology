<?php
namespace Jus\Core;

interface UrlInterface
{
	/**
	 * @param string $name
	 * @param $value
	 * @return UrlInterface
	 */
	public function withParam($name, $value);

	/**
	 * @param string $name
	 * @return UrlInterface
	 */
	public function withoutParam($name);

	/**
	 * @param string $path
	 * @return UrlInterface
	 */
	public function withPath($path);

	/**
	 * @param \Url $url
	 * @return string
	 */
	public function url(\Url $url);
}
