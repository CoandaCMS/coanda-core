<?php namespace CoandaCMS\Coanda\Pages\Presenters;

use Lang;

class PageVersion extends \CoandaCMS\Coanda\Core\Presenters\Presenter {

	public function status()
	{
		return Lang::get('coanda::pages.status_' . $this->model->status);
	}

}