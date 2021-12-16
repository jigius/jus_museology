<?php

namespace Jus\App\Museology\Template\PersistedLayer\Session;

use Jus\Core\FormInterface;
use Jus\Foundation\PrinterInterface;
use LogicException;
use DomainException;

/**
 * Printer class
 */
final class SessionPrn implements PrinterInterface
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
        $this->i = [
            'payload' => []
        ];
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
     * @return void
     */
    public function finished()
    {
        if (!isset($this->i['session']) || !is_a($this->i['session'], \Session::class)) {
            throw new LogicException("invalid type");
        }
        if (!isset($this->i['form']) || !$this->i['form'] instanceof FormInterface) {
            throw new LogicException("invalid type");
        }
	    if (!isset($this->i['action']) || !in_array($this->i['action'], ['push', 'remove'])) {
		    throw new LogicException("invalid type");
	    }
        if (!isset($this->i['session']->data['_preload'])) {
            $this->i['session']->data['_preload'] = [];
        }
		if ($this->i['action'] === "push") {
			$this->i['session']->data['_preload'][$this->i['form']->uid()] = $this->i['payload'];
		} elseif (
			$this->i['action'] === "remove" &&
			isset($this->i['session']->data['_preload'][$this->i['form']->uid()])
		) {
			unset($this->i['session']->data['_preload'][$this->i['form']->uid()]);
		}
    }
    
    /**
     * Clones the instance
     * @return $this
     */
    public function blueprinted()
    {
        $that = new self();
        $that->i = $this->i;
        return $that;
    }
}
