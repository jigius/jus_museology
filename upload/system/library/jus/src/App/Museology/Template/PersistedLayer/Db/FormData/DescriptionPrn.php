<?php

namespace Jus\App\Museology\Template\PersistedLayer\Db\FormData;

use Jus\Foundation\PrinterInterface;
use LogicException;
use DomainException;

/**
 * Printer class
 * Prints out a description for specified language from template's data has gotten for specified category id
 * The result is a string or throws an exception
 */
final class DescriptionPrn implements PrinterInterface
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
     * @throws LogicException|DomainException
     */
    public function finished()
    {
        if (!isset($this->i['languageId']) || !is_numeric($this->i['languageId'])) {
            throw new LogicException("type invalid");
        }
    
        if (!isset($this->i['chunk']) || !is_array($this->i['chunk'])) {
            throw new LogicException("type invalid");
        }
        $res = null;
        foreach ($this->i['chunk'] as $itm) {
            if (!isset($itm['language_id'])) {
                throw new DomainException("invalid data");
            }
            if ($itm['language_id'] != $this->i['languageId']) {
                continue;
            }
            $res = $itm['category_name'];
        }
        return $res;
    }
    
    /**
     * Clones the instance
     * @return DescriptionPrn
     */
    public function blueprinted()
    {
        $that = new self();
        $that->i = $this->i;
        return $that;
    }
}
