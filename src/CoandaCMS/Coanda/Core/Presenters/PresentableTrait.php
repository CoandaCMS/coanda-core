<?php namespace CoandaCMS\Coanda\Core\Presenters;

use CoandaCMS\Coanda\Core\Presenters\Exceptions\PresenterException;

/**
 * Class PresentableTrait
 * @package CoandaCMS\Coanda\Core\Presenters
 */
trait PresentableTrait {

	/**
	 * View presenter instance
	 *
	 * @var mixed
	 */
	protected $presenter;

	/**
	 * Prepare a new or cached presenter instance
	 *
	 * @return mixed
	 * @throws PresenterException
	 */
	public function present()
	{
		if ( !$this->presenter)
		{
			throw new PresenterException('Please set the $presenter property to your presenter path.');
		}

		if (!class_exists($this->presenter))
		{
			throw new PresenterException('Class specified for $presenter property does not exist.');
		}

		if ( ! $this->presenterInstance)
		{
			$this->presenterInstance = new $this->presenter($this);
		}

		return $this->presenterInstance;
	}

} 