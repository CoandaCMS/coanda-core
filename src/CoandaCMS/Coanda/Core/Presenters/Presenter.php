<?php namespace CoandaCMS\Coanda\Core\Presenters;

abstract class Presenter {

	/**
	 * @var mixed
	 */
	protected $model;

	/**
	 * @param $model
	 */
	function __construct($model)
	{
		$this->model = $model;
	}

	/**
	 * Allow for property-style retrieval
	 *
	 * @param $property
	 * @return mixed
	 */
	public function __get($property)
	{
		if (method_exists($this, $property))
		{
			return $this->{$property}();
		}

		return $this->model->{$property};
	}

} 