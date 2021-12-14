<?php

namespace Jus\Core;

interface ResponseInterface
{
	/**
	 * @param UrlInterface $url
	 * @return ResponseInterface
	 */
	public function withUrl(UrlInterface $url);

	/**
	 * @return UrlInterface
	 */
	public function url();

	/**
	 * @param \Url $url
	 * @param int $code
	 * @return ResponseInterface
	 */
	public function withRedirect(\Url $url, $code = 302);

	/**
	 * @param string $bytes
	 * @return ResponseInterface
	 */
	public function withOutput($bytes);

	/**
	 * @return void
	 */
	public function process();
}
