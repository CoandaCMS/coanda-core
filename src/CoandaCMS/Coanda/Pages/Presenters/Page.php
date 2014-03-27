<?php namespace CoandaCMS\Coanda\Pages\Presenters;

use Lang;

class Page extends \CoandaCMS\Coanda\Core\Presenters\Presenter {

	public function name()
	{
		if ($this->model->name !== '')
		{
			return $this->model->name;
		}

		return Lang::get('coanda::pages.page_name_not_set');
	}

	public function status()
	{
		return Lang::get('coanda::pages.status_' . $this->model->status);
	}

	public function type()
	{
		return $this->model->pageType()->name;
	}

}