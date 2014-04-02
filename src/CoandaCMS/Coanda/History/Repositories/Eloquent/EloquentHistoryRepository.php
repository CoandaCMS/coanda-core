<?php namespace CoandaCMS\Coanda\History\Repositories\Eloquent;

use Coanda;

use CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface;

use CoandaCMS\Coanda\History\Repositories\Eloquent\Models\History as HistoryModel;

class EloquentHistoryRepository implements HistoryRepositoryInterface {

	private $model;

	public function __construct(HistoryModel $model)
	{
		$this->model = $model;
	}

	/**
	 * Adds a new history recrod
	 * @param string $for
	 * @param integer $for_id
	 * @param integer $user_id
	 * @param mixed $data
	 */
	public function add($for, $for_id, $user_id, $data)
	{
		$history = new $this->model;
		$history->for = $for;
		$history->for_id = $for_id;
		$history->user_id = $user_id;
		$history->data = $data;

		$history->save();

		return $history;
	}

	/**
	 * Returns all the history for a specified for and for_id
	 * @param  string $for
	 * @param  integer $for_id
	 * @return
	 */
	public function get($for, $for_id)
	{
		return $this->model->whereFor($for)->whereForId($for_id)->orderBy('created_at', 'desc')->get();
	}

}