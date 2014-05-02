<?php namespace CoandaCMS\Coanda\Layout\Presenters;

use Lang;
use Carbon\Carbon;

class LayoutBlockVersion extends \CoandaCMS\Coanda\Core\Presenters\Presenter {

    public function status()
	{
		return Lang::get('coanda::layout.status_' . $this->model->status);
	}

    public function visible_from_date()
    {
        if (!$this->model->visible_from)
        {
            return '';
        }

        return $this->format_date('visible_from', 'd/m/Y');
    }

    public function visible_from_time()
    {
        if (!$this->model->visible_from)
        {
            return '';
        }

        return $this->format_date('visible_from', 'h:i');
    }

    public function visible_from()
    {
        if (!$this->model->visible_from)
        {
            return '';
        }

        return $this->format_date('visible_from');
    }

    public function visible_to()
    {
        if (!$this->model->visible_to)
        {
            return '';
        }

        return $this->format_date('visible_to');
    }

}