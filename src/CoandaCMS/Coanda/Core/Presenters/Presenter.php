<?php namespace CoandaCMS\Coanda\Core\Presenters;

/**
 * Class Presenter
 * @package CoandaCMS\Coanda\Core\Presenters
 */
abstract class Presenter {

    /**
     * @var string
     */
    protected $date_format = 'd/m/Y H:i';

	/**
	 * @var mixed
	 */
	protected $model;

	/**
	 * @param $model
	 */
	public function __construct($model)
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

    /**
     * @return string
     */
    public function updated_at()
	{
		return $this->format_date('updated_at');
	}

    /**
     * @return string
     */
    public function created_at()
	{
		return $this->format_date('created_at');
	}

    /**
     * @param $property
     * @return string
     */
    public function format_date($property, $format = false)
	{
		if ($this->model->$property && method_exists($this->model->$property, 'format'))
		{
			if (!$format)
			{
				$format = $this->date_format;
			}
			
			return $this->model->$property->format($format);
		}
		
		return '';
	}
} 