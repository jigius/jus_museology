<?php
    
namespace Jus\Core;

use Jus\Foundation as F;

interface FormInterface extends F\MediaInterface
{
	/**
	 * Appends URL for form's action
	 * @param UrlInterface $url
	 * @return FormInterface
	 */
    public function withAction(UrlInterface $url);

	/**
	 * Return a unique id of the form
	 * @param \Url $url
	 * @return string
	 */
    public function uid(\Url $url);

	/**
	 * @param FieldsInterface $f
	 * @return FormInterface
	 */
	public function withFields(FieldsInterface $f);

	/**
	 * @return FieldsInterface
	 */
	public function fields();
}
