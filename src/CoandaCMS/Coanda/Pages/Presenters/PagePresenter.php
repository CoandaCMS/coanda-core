<?php namespace CoandaCMS\Coanda\Pages\Presenters;

use Lang;

class PagePresenter extends ModelPresenter {

	public function getName()
	{
		if ($this->model->name !== '')
		{
			$this->model->name;
		}

		return Lang::get('coanda::pages.page_name_not_set');
	}

	public function getStatus()
	{
		return Lang::get('coanda::pages.status_' . $this->model->status);
	}
	
}