<?php
namespace Jus\App\Museology;

use Jus\Core\Illuminate\Printer\WithSuppressedFinishedPrn;
use Jus\Foundation as F;
use Jus\Core as C;
use Url;
use LogicException;

/**
 * Trivial implementation of form
 */
final class Form implements C\FormInterface
{
    /**
     * @var array
     */
    private $i;
    
    public function __construct()
    {
        $this->i = [
			'fields' => new C\Fields()
        ];
    }

    /**
     * @inheritDoc
     */
    public function withAction(C\UrlInterface $url)
    {
        $that = $this->blueprinted();
        $that->i['action'] = $url;
        return $that;
    }

    /**
     * @inheritDoc
     * @throw LogicException
     */
    public function uid(Url $url)
    {
        if (!isset($this->i['action']) || !$this->i['action'] instanceof C\UrlInterface) {
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
	public function withFields(F\FieldsInterface $f)
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
	public function printed(F\PrinterInterface $printer)
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
