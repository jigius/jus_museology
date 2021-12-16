<?php

namespace Jus\Foundation;

interface AttributableInterface
{
	/**
	 * @param AttributesInterface $a
	 * @return AttributableInterface
	 */
	public function withAttrs(AttributesInterface $a);

	/**
	 * @return AttributesInterface
	 */
	public function attrs();
}
