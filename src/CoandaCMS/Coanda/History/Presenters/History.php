<?php namespace CoandaCMS\Coanda\History\Presenters;

use Lang;

/**
 * Class History
 * @package CoandaCMS\Coanda\History\Presenters
 */
class History extends \CoandaCMS\Coanda\Core\Presenters\Presenter {

    /**
     * @return string
     */
    public function avatar()
	{
		$user = $this->model->user;

		if ($user)
		{
			return $user->avatar;
		}

		return 'http://www.gravatar.com/avatar/dummy?d=mm';
	}

    /**
     * @return string
     */
    public function username()
	{
		$user = $this->model->user;

		if ($user)
		{
			return $user->present()->name;
		}

		return 'System';
	}

    /**
     * @return string
     */
    public function happening()
	{
		switch ($this->model->action)
		{
			case 'initial_version':
			{
				return 'created the page';
			}

			case 'new_version':
			{
				return 'created version #' . $this->model->action_data->version;
			}

			case 'discard_version':
			{
				return 'discarded version #' . $this->model->action_data->version;
			}

			case 'publish_version':
			{
				return 'published version #' . $this->model->action_data->version;
			}

			case 'order_changed':
			{
				return 'order changed to ' . $this->model->action_data->new_order;
			}

			case 'restored':
			{
				return 'restored';
			}

			case 'trashed':
			{
				return 'trashed';
			}

			default:
			{
				return $this->model->action;
			}
		}
	}
}