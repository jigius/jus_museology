<?php

namespace Jus\Core;

use Jus\Foundation\MediaInterface;

interface MessageInterface extends MediaInterface
{
	const CONTAINER_NAME = "msg";

	const TYPE_SUCCESS = 0;
	const TYPE_WARNING = 1;
	const TYPE_ERROR = 2;

	/**
	 * Appends data about a message
	 * @param int $type
	 * @param string $txt
	 * @return MessageInterface
	 */
	public function with($type, $txt);

	/**
	 * Returns an empty instance
	 * @return MessageInterface
	 */
	public function cleaned();
}
