<?php namespace CoandaCMS\Coanda\Users\Presenters;

use Lang;

class User extends \CoandaCMS\Coanda\Core\Presenters\Presenter {

	public function name()
	{
		return $this->model->first_name . ' ' . $this->model->last_name;
	}

}