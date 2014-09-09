<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Presenters;

use Carbon\Carbon;
use CoandaCMS\Coanda\Core\Presenters\Presenter;
use Lang;

/**
 * Class Page
 * @package CoandaCMS\Coanda\Pages\Presenters
 */
class Page extends Presenter {

    /**
     * @return mixed
     */
    public function name()
	{
		if ($this->model->name !== '')
		{
			return htmlspecialchars($this->model->name);
		}

		$generated_name = $this->model->pageType()->generateName($this->model->versions()->first());

		if ($generated_name !== '')
		{
			return htmlspecialchars($generated_name);
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
		return $this->model->pageType()->name();
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

		$now = Carbon::now(date_default_timezone_get());

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
			$visibility_text .= ' until ' . $to->format('d/m/Y H:i');
		}

		return $visibility_text;
	}

    /**
     * @return string
     */
    public function visible_dates_short()
	{
		$from = $this->model->visible_from;
		$to = $this->model->visible_to;

		$now = Carbon::now(date_default_timezone_get());

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