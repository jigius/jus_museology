<?php
namespace Jus\App\Museology\Template;

use Jus\Core\FormInterface;
use LogicException;

final class Form implements FormInterface
{
    /**
     * @var array
     */
    private $i;
    
    public function __construct()
    {
        $this->i = [];
    }

    /**
     * @inheritDoc
     */
    public function withAction($action)
    {
        $that = $this->blueprinted();
        $that->i['action'] = $action;
        return $that;
    }

    /**
     * @inheritDoc
     * @throw LogicException
     */
    public function uid()
    {
        if (!isset($this->i['action']) || !is_string($this->i['action'])) {
            throw new LogicException("invalid type");
        }
        return hash('sha1', strtolower($this->i['action']));
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
