<?php
    
namespace Jus\Core;

interface FormInterface
{
    /**
     * Appends an action for form
     * @param string $action
     * @return FormInterface
     */
    public function withAction($action);

    /**
     * Return a unique id of the form
     * @return string
     */
    public function uid();
}
