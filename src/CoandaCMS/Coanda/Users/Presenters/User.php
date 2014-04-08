<?php namespace CoandaCMS\Coanda\Users\Presenters;

use Lang;

/**
 * Class User
 * @package CoandaCMS\Coanda\Users\Presenters
 */
class User extends \CoandaCMS\Coanda\Core\Presenters\Presenter {

    /**
     * @return string
     */
    public function name()
	{
		return $this->model->first_name . ' ' . $this->model->last_name;
	}

}