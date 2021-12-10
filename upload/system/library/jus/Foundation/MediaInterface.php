<?php

namespace Jus\Foundation;

interface MediaInterface
{
	/**
	 * @param PrinterInterface $printer
	 * @return mixed
	 */
	public function printed(PrinterInterface $printer);
}
