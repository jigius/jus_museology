<?php
namespace Jus\Core\Illuminate\Printer;

use Jus\Foundation as F;

/**
 * Class ArrayPrn
 * Prints out a media into an array
 */
final class ArrayPrn implements F\PrinterInterface
{
    /**
     * An initial value
     * @var array
     */
    private $i;

    /**
     * ArrayPrinter constructor.
     * @param array $i|[]
     */
    public function __construct(array $i = [])
    {
        $this->i = $i;
    }

    /**
     * @inheritDoc
     */
    public function with($key, $val)
    {
        $obj = $this->blueprinted();
        $obj->i[$key] = $val;
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function finished()
    {
        return $this->i;
    }

	/**
	 * Clones the instance
	 * @return ArrayPrn
	 */
    private function blueprinted()
    {
        return new self($this->i);
    }
}
