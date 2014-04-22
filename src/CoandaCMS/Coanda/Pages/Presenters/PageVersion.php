<?php namespace CoandaCMS\Coanda\Pages\Presenters;

use Lang;
use Carbon\Carbon;

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

    /**
     * @return Carbon
     */
    public function delayed_publish_date()
    {
        if ($this->model->publish_handler == 'delayed')
        {
            $data = json_decode($this->model->publish_handler_data);

            if (isset($data->date))
            {
                return Carbon::createFromFormat('Y-m-d H:i:s', $data->date->date, $data->date->timezone);
            }
        }
    }
}