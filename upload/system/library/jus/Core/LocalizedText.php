<?php

namespace Jus\Core;

use Language;

class LocalizedText implements TransformableTextInterface
{
	/**
	 * @var Language
	 */
	private $l;

	/**
	 * Cntr
	 * @param Language $l
	 */
	public function __construct(Language $l)
	{
		$this->l = $l;
	}
	/**
	 * @inheritDoc
	 */
	public function fetch($key)
	{
		return $this->l->get($key);
	}

	/**
	 * @inheritDoc
	 */
	public function pushed($key, $val)
	{
		$that = $this->blueprinted();
		$that->l->set($key, $val);
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function withLoaded($filename, $realm = '')
	{
		$that = $this->blueprinted();
		$that->l->load($filename, $realm);
		return $that;
	}

	/**
	 * Clones the instance
	 * @return LocalizedText
	 */
	public function blueprinted()
	{
		return new self($this->l);
	}
}
