<?php

namespace Jus\Core\Illuminate\Printer;

use Jus\Foundation\PrinterInterface;

/**
 * Suppresses the finished-method
 */
final class WithSuppressedFinishedPrn implements SuppressedFinishedInterface
{
	/**
	 * @var PrinterInterface
	 */
	private $original;

	public function __construct(PrinterInterface $p)
	{
		$this->original = $p;
	}

	/**
	 * @inheritDoc
	 */
	public function with($key, $val)
	{
		$that = $this->blueprinted();
		$that->original = $this->original->with($key, $val);
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function finished()
	{
		return $this->original;		
	}

	/**
	 * @inheritDoc
	 */
	public function original()
	{
		return $this->original;
	}

	/**
	 * Clones the instance
	 * @return $this
	 */
	public function blueprinted()
	{
		return new self($this->original);
	}
}
