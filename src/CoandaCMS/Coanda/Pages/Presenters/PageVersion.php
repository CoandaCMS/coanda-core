<?php namespace CoandaCMS\Coanda\Pages\Presenters;

use Lang;

/**
 * Class PageVersion
 * @package CoandaCMS\Coanda\Pages\Presenters
 */
class PageVersion extends \CoandaCMS\Coanda\Core\Presenters\Presenter {

    /**
     * @return mixed
     */
    public function status()
	{
		return Lang::get('coanda::pages.status_' . $this->model->status);
	}

    /**
     * @return string
     */
    public function preview_url()
	{
		return 'pages/preview/' . $this->model->preview_key;
	}

}