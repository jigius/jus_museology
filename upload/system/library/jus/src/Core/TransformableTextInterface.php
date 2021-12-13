<?php

namespace Jus\Core;

/*
 * Fetches text with some transformation
 */
interface TransformableTextInterface
{
	/**
	 * Fetches an entry with specified key
	 * @param string $key
	 * @return string
	 */
	public function fetch($key);

	/**
	 * Adds a new entry
	 * @param string $key
	 * @param string $val
	 * @return TransformableTextInterface
	 */
	public function pushed($key, $val);

	/**
	 * Loads data from a file
	 * @param string $filename
	 * @param string $realm
	 * @return TransformableTextInterface
	 */
	public function withLoaded($filename, $realm = '');
}
