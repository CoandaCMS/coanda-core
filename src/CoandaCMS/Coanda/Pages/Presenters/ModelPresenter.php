<?php namespace CoandaCMS\Coanda\Pages\Presenters;

abstract class ModelPresenter {
	
	protected $model;

	public function setModel($model)
	{
		$this->model = $model;
	}

	public function __get($attribute)
	{
		$method_name = camel_case('get_' . $attribute);

		if (method_exists($this, $method_name))
		{			
			return $this->$method_name();
		}

		return $this->model->$attribute;
	}

	public function __call($method_name, $arguments)
	{
		if (method_exists($this, $method_name))
		{
			return $this->$method_name($arguments);
		}

		return $this->model->$method_name($arguments);
	}

}