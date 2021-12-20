<?php
namespace Jus\Core\Illuminate;

use Jus\Foundation as F;

/**
 * Class ArrayMedia
 * Makes possible to print out an injected value into a printer
 */
final class ArrayMedia implements F\MediaInterface
{
    /**
     * @var array
     */
    private $i;

    /**
     * Cntr
     * @param array $i
     */
    public function __construct(array $i)
    {
        $this->i = $i;
    }

    /**
     * @inheritDoc
     */
    public function printed(F\PrinterInterface $printer)
    {
        foreach ($this->i as $k => $v) {
            $printer = $printer->with($k, $v);
        }
        return $printer->finished();
    }
}
