<?php

namespace Jus\Foundation;

/**
 * Interface SerializableInterface
 * Adds capability to an instance to be serialized to and unserialized from a string
 */
interface SerializableInterface
{

	/**
	 * Creates an object from its serialized state (in form of an array)
	 * @param array $data
	 * @return mixed
	 */
	public function unserialized($data);

	/**
	 * Creates a serialized object's state in form of an array
	 * @return array
	 */
	public function serialized();
}
