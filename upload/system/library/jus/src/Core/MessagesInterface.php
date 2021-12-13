<?php
namespace Jus\Core;

use Jus\Foundation\MediaInterface;

interface MessagesInterface extends MediaInterface
{
	/**
	 * Appends data about a message
	 * @param int $type
	 * @param string $txt
	 * @return MessagesInterface
	 */
	public function with($type, $txt);

	/**
	 * Returns an empty instance
	 * @return MessagesInterface
	 */
	public function cleaned();
}
