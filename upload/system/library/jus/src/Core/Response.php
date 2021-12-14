<?php

namespace Jus\Core;

use LogicException;

class Response implements ResponseInterface
{
	/**
	 * @var array
	 */
	private $i;
	/**
	 * @var \Response
	 */
	private $r;

	/**
	 * @param \Response $r
	 */
	public function __construct(\Response $r)
	{
		$this->r = $r;
		$this->i = [
			'bytes' => "",
			'redirect' => false
		];
	}

	/**
	 * @inheritDoc
	 */
	public function withUrl(UrlInterface $url)
	{
		$that = $this->blueprinted();
		$that->i['url'] = $url;
		return $that;
	}

	/**
	 * @inheritDoc
	 * @throws LogicException
	 */
	public function url()
	{
		if (!isset($this->i['url'])) {
			throw new LogicException("not defined");
		}
		return $this->i['url'];
	}

	/**
	 * @inheritDoc
	 */
	public function withRedirect(\Url $url, $code = 302)
	{
		$that = $this->blueprinted();
		$that->i['redirect'] = true;
		$that->i['stockUrl'] = $url;
		$that->i['redirectCode'] = $code;
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function withOutput($bytes)
	{
		$that = $this->blueprinted();
		$that->i['bytes'] = $bytes;
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function process()
	{
		if (isset($this->i['redirect']) && $this->i['redirect']) {
			$this->r->redirect($this->url()->url($this->i['stockUrl']), $this->i['redirectCode']);
		} else {
			$this->r->setOutput($this->i['bytes']);
		}
	}

	/**
	 * Clones the instance
	 * @return Response
	 */
	public function blueprinted()
	{
		$that = new self($this->r);
		$that->i = $this->i;
		return $that;
	}
}
