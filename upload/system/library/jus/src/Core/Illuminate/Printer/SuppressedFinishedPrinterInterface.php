<?php

namespace Jus\Core\Illuminate\Printer;

use Jus\Foundation as F;

interface SuppressedFinishedPrinterInterface extends F\PrinterInterface, F\DecoratorInterface
{
	/**
	 * @inheritDoc
	 * @return F\PrinterInterface
	 */
	public function finished();

	/**
	 * @inheritDoc
	 * @return F\PrinterInterface
	 */
	public function original();
}
