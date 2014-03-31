<?php namespace CoandaCMS\Coanda\Core\Presenters;

abstract class Presenter {

	protected $date_format = 'd/m/Y H:i';

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

	public function updated_at()
	{
		return $this->format_date('updated_at');
	}

	public function created_at()
	{
		return $this->format_date('created_at');
	}

	public function format_date($property)
	{
		if ($this->model->$property && method_exists($this->model->$property, 'format'))
		{
			return $this->model->$property->format($this->date_format);
		}
		
		return '';
	}
} 