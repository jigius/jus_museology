<?php
namespace Jus\App\Museology;

use Jus\Core\Illuminate\Printer\WithSuppressedFinishedPrn;
use Jus\Foundation\FieldsInterface;
use Jus\Core\FormInterface;
use Jus\Core\UrlInterface;
use Jus\Foundation\PrinterInterface;
use LogicException;

final class Form implements FormInterface
{
    /**
     * @var array
     */
    private $i;
    
    public function __construct()
    {
        $this->i = [
			'fields' => new Fields()
        ];
    }

    /**
     * @inheritDoc
     */
    public function withAction(UrlInterface $url)
    {
        $that = $this->blueprinted();
        $that->i['action'] = $url;
        return $that;
    }

    /**
     * @inheritDoc
     * @throw LogicException
     */
    public function uid(\Url $url)
    {
        if (!isset($this->i['action']) || !$this->i['action'] instanceof UrlInterface) {
            throw new LogicException("invalid type");
        }
        return
	        hash(
				'sha1',
				parse_url(
					$this->i['action']->url($url),
					PHP_URL_PATH
				)
	        );
    }

	/**
	 * @inheritDoc
	 */
	public function withFields(FieldsInterface $f)
	{
		$that = $this->blueprinted();
		$that->i['fields'] = $f;
		return $that;
	}

	/**
	 * @inheritDoc
	 */
	public function fields()
	{
		return $this->i['fields'];
	}

	/**
	 * @inheritDoc
	 */
	public function printed(PrinterInterface $printer)
	{
		$p = new WithSuppressedFinishedPrn($printer);
		$this
			->i['fields']
			->each(function ($field) use (&$p) {
				$p = $field->printed($p);
			});
		return $p->original()->finished();
	}

	/**
     * Clones the instance
     * @return $this
     */
    public function blueprinted()
    {
        $that = new self();
        $that->i = $this->i;
        return $that;
    }
}
