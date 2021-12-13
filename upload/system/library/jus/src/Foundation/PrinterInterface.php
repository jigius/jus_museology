<?php
namespace Jus\Foundation;

interface PrinterInterface
{
	/**
	 * @param string $key
	 * @param mixed $val
	 * @return PrinterInterface
	 */
	public function with($key, $val);

	/**
	 * @return mixed
	 */
	public function finished();
}
