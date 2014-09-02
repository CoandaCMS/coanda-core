<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Presenters;

use Lang;

/**
 * Class PageLocation
 * @package CoandaCMS\Coanda\Pages\Presenters
 */
class PageLocation extends \CoandaCMS\Coanda\Core\Presenters\Presenter {

	private $order_names = [
		'manual' => 'Manual',
		'alpha:asc' => 'Alpabetical (A-Z)',
		'alpha:desc' => 'Alpabetical (Z-A)',
		'created:desc' => 'Created date (Newest-Oldest)',
		'created:asc' => 'Created date (Oldest-Newest)',
	];

    /**
     * @return mixed
     */
    public function sub_location_order()
	{
		return $this->order_names[$this->model->sub_location_order];
	}

	public function path()
	{
		$path_array = [];

		foreach ($this->model->parents() as $parent)
		{
			$path_array[] = $parent->present()->name;
		}

		$path_array[] = $this->model->present()->name;

		return implode(' / ', $path_array);
	}
}