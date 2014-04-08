<?php namespace CoandaCMS\Coanda\Pages\Presenters;

use Lang;

/**
 * Class Page
 * @package CoandaCMS\Coanda\Pages\Presenters
 */
class Page extends \CoandaCMS\Coanda\Core\Presenters\Presenter {

    /**
     * @return mixed
     */
    public function name()
	{
		if ($this->model->name !== '')
		{
			return $this->model->name;
		}

		return Lang::get('coanda::pages.page_name_not_set');
	}

    /**
     * @return string
     */
    public function status()
	{
		if ($this->model->is_trashed)
		{
			return 'Trashed';
		}
		
		return Lang::get('coanda::pages.status_' . $this->model->status);
	}

    /**
     * @return mixed
     */
    public function type()
	{
		return $this->model->pageType()->name;
	}

}