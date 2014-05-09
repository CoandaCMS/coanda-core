<?php namespace CoandaCMS\Coanda\Layout\Presenters;

use Lang;
use Carbon\Carbon;

/**
 * Class LayoutBlockVersion
 * @package CoandaCMS\Coanda\Layout\Presenters
 */
class LayoutBlockVersion extends \CoandaCMS\Coanda\Core\Presenters\Presenter {

    /**
     * @return mixed
     */
    public function status()
	{
		return Lang::get('coanda::layout.status_' . $this->model->status);
	}

    /**
     * @return string
     */
    public function visible_from_date()
    {
        if (!$this->model->visible_from)
        {
            return '';
        }

        return $this->format_date('visible_from', 'd/m/Y');
    }

    /**
     * @return string
     */
    public function visible_from_time()
    {
        if (!$this->model->visible_from)
        {
            return '';
        }

        return $this->format_date('visible_from', 'h:i');
    }

    /**
     * @return string
     */
    public function visible_from()
    {
        if (!$this->model->visible_from)
        {
            return '';
        }

        return $this->format_date('visible_from');
    }

    /**
     * @return string
     */
    public function visible_to()
    {
        if (!$this->model->visible_to)
        {
            return '';
        }

        return $this->format_date('visible_to');
    }

}