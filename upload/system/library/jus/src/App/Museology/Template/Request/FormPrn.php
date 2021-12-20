<?php
namespace Jus\App\Museology\Template\Request;

use Jus\Foundation as F;

class FormPrn implements F\PrinterInterface
{
	/**
	 * @var array
	 */
	private $i;

	/**
	 * Cntr
	 */
	public function __construct()
	{
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
	 */
	public function finished()
	{

	}

	/**
	 * Clones the instance
	 * @return FormPrn
	 */
	public function blueprinted()
	{
		$that = new self();
		$that->i = $this->i;
		return $that;
	}
}