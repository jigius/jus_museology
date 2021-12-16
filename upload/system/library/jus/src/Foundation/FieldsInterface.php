<?php

namespace Jus\Foundation;

interface FieldsInterface extends MediaInterface
{
	/**
	 * Adds a field
	 * @param FieldInterface $f
	 * @param string $id Optional ID for the field
	 * @return FieldsInterface
	 */
	public function with(FieldInterface $f, $id = "");

	/**
	 * @param string $id
	 * @return FieldsInterface
	 */
	public function without($id);

	/**
	 * @param callable $callee
	 * @return void
	 */
	public function each($callee);
}
