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

    /**
     * @return string
     */
    public function visibility()
	{
		return $this->model->is_visible ? 'Visble' : 'Hidden';
	}

    /**
     * @return string
     */
    public function visible_dates()
	{
		$from = $this->model->visible_from;
		$to = $this->model->visible_to;

		$now = \Carbon\Carbon::now(date_default_timezone_get());

		$visibility_text = 'Visible';

		if ($from)
		{
			if ($from->gt($now))
			{
				$visibility_text .= ' from ' . $from->format('d/m/Y H:i');
			}
		}

		if ($to)
		{
			if ($to->gt($now))
			{
				$visibility_text .= ' until ' . $to->format('d/m/Y H:i');
			}			
		}

		$visibility_text .= ' (System time: ' . $now->format('d/m/Y H:i') . ')';

		return $visibility_text;
	}

    /**
     * @return string
     */
    public function visible_dates_short()
	{
		$from = $this->model->visible_from;
		$to = $this->model->visible_to;

		$now = \Carbon\Carbon::now(date_default_timezone_get());

		$visibility_text = '';
		
		if ($from)
		{
			if ($from->gt($now))
			{
				$visibility_text .= ' from ' . $from->format('d/m/Y H:i');
			}
		}

		if ($to)
		{
			if ($to->gt($now))
			{
				$visibility_text .= ' until ' . $to->format('d/m/Y H:i');
			}			
		}

		return $visibility_text;
	}
}