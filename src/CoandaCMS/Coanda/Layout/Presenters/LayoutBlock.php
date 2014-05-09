<?php namespace CoandaCMS\Coanda\Layout\Presenters;

use Lang;

/**
 * Class LayoutBlock
 * @package CoandaCMS\Coanda\Layout\Presenters
 */
class LayoutBlock extends \CoandaCMS\Coanda\Core\Presenters\Presenter {

    /**
     * @return mixed
     */
    public function name()
	{
		if ($this->model->name !== '')
		{
			return $this->model->name;
		}

		return Lang::get('coanda::layout.name_not_set');
	}

    /**
     * @return mixed
     */
    public function status()
	{
		return Lang::get('coanda::layout.status_' . $this->model->status);
	}

    /**
     * @return mixed
     */
    public function type()
	{
		return $this->model->blockType()->name();
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