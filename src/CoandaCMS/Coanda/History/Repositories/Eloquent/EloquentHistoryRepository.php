<?php namespace CoandaCMS\Coanda\History\Repositories\Eloquent;

use CoandaCMS\Coanda\History\Repositories\Eloquent\Models\HistoryDigestSubscriber;
use CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface;
use CoandaCMS\Coanda\History\Repositories\Eloquent\Models\History as HistoryModel;
use CoandaCMS\Coanda\Users\UserManager;

/**
 * Class EloquentHistoryRepository
 * @package CoandaCMS\Coanda\History\Repositories\Eloquent
 */
class EloquentHistoryRepository implements HistoryRepositoryInterface {

    /**
     * @var Models\History
     */
    private $model;

	/**
	 * @var HistoryDigestSubscriber
     */
	private $digest_subscriber_model;

	/**
	 * @var UserManager
     */
	private $user_manager;

    /**
     * @param HistoryModel $model
     * @param UserManager $user_manager
     */
    public function __construct(HistoryModel $model, HistoryDigestSubscriber $digest_subscriber_model, UserManager $user_manager)
	{
		$this->model = $model;
		$this->digest_subscriber_model = $digest_subscriber_model;
        $this->user_manager = $user_manager;
	}

    /**
     * Adds a new history record
     * @param string $for
     * @param integer $for_id
     * @param integer $user_id
     * @param $action
     * @param mixed $data
     * @return mixed
     */
	public function add($for, $for_id, $user_id, $action, $data = '')
	{
		$history = new $this->model;
		$history->for = $for;
		$history->for_id = $for_id;
		$history->user_id = $user_id;
		$history->action = $action;
		$history->data = is_array($data) ? json_encode($data) : $data;

		$history->save();

		return $history;
	}

    /**
     * Returns all the history for a specified for and for_id
     * @param  string $for
     * @param  integer $for_id
     * @param bool $limit
     * @return mixed
     */
	public function get($for, $for_id, $limit = false)
	{
		return $this->model->whereFor($for)->whereForId($for_id)->orderBy('created_at', 'desc')->take($limit)->get();
	}

    /**
     * @param $for
     * @param $for_id
     * @param int $limit
     * @return mixed
     */
    public function getPaginated($for, $for_id, $limit = 10)
	{
		return $this->model->whereFor($for)->whereForId($for_id)->orderBy('created_at', 'desc')->paginate($limit);
	}

	/**
	 * @param int $limit
	 * @return mixed
     */
	public function getAllPaginated($limit = 10)
	{
		return $this->model->where('user_id', '<>', 0)->orderBy('created_at', 'desc')->paginate($limit);
	}

	/**
	 * @param $from
	 * @param $to
	 * @param int $limit
	 * @return mixed
     */
	public function getAllPaginatedByTimePeriod($from, $to, $limit = 10)
	{
		return $this->model->where('created_at', '>', $from)->where('created_at', '<', $to)->where('user_id', '<>', 0)->orderBy('created_at', 'desc')->paginate($limit);
	}

	/**
	 * @return mixed
     */
	private function yesterdayQuery()
	{
		return \DB::raw('DATE_SUB(CURDATE(), INTERVAL 1 day) AND CURDATE()');
	}

	/**
	 * @return mixed
     */
	private function todayQuery()
	{
		return \DB::raw('CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 day)');
	}

	/**
	 * @return mixed
     */
	private function thisWeekQuery()
	{
		return \DB::raw('DATE_ADD(CURDATE(), INTERVAL 1-DAYOFWEEK(CURDATE()) DAY) AND DATE_ADD(CURDATE(), INTERVAL 7-DAYOFWEEK(CURDATE()) DAY)');
	}

	/**
	 * @return mixed
     */
	private function thisMonthQuery()
	{
		return \DB::raw('DATE_SUB(CURDATE(),INTERVAL (DAY(CURDATE())-1) DAY) AND LAST_DAY(NOW())');
	}

	/**
	 * @return mixed
     */
	private function thisYearQuery()
	{
		return \DB::raw('MAKEDATE(YEAR(CURDATE()),1)');
	}

	/**
	 * @return array
     */
	public function getActivitySummaryFigures()
	{
		return [
			'today' => $this->todayFigures(),
			'week' => $this->weeklyFigures(),
			'month' => $this->monthlyFigures(),
			'year' => $this->yearlyFigures()
		];
	}

	/**
	 * @return array
     */
	public function getDigestSummaryFigures()
	{
		return [
			'yesterday' => $this->yesterdayFigures(),
			'week' => $this->weeklyFigures(),
			'month' => $this->monthlyFigures(),
			'year' => $this->yearlyFigures()
		];
	}

	/**
	 * @return mixed
     */
	private function yesterdayFigures()
	{
		return $this->model->where('user_id', '<>', 0)->where('created_at', 'BETWEEN', $this->yesterdayQuery())->count();
	}

	/**
	 * @return mixed
     */
	private function todayFigures()
	{
		return $this->model->where('user_id', '<>', 0)->where('created_at', 'BETWEEN', $this->todayQuery())->count();
	}

	/**
	 * @return mixed
     */
	private function weeklyFigures()
	{
		return $this->model->where('user_id', '<>', 0)->where('created_at', 'BETWEEN', $this->thisWeekQuery())->count();
	}

	/**
	 * @return mixed
     */
	private function monthlyFigures()
	{
		return $this->model->where('user_id', '<>', 0)->where('created_at', 'BETWEEN', $this->thisMonthQuery())->count();
	}

	/**
	 * @return mixed
     */
	private function yearlyFigures()
	{
		return $this->model->where('user_id', '<>', 0)->where('created_at', '>', $this->thisYearQuery())->count();
	}

    /**
     * @param $for
     * @param $for_id
     * @return mixed
     */
    public function users($for, $for_id)
	{
		return $this->user_manager->getByIds($this->model->whereFor($for)->whereForId($for_id)->groupBy('user_id')->lists('user_id'));
	}

	/**
	 * @param $user_id
	 * @return mixed
     */
	public function getDigestSubscriber($user_id)
	{
		return $this->digest_subscriber_model->where('user_id', $user_id)->first();
	}

	/**
	 * @param $limit
	 * @param $offset
	 * @return mixed
     */
	public function getDigestSubscribers($limit, $offset)
	{
		return $this->digest_subscriber_model->take($limit)->skip($offset)->get();
	}

	/**
	 * @param $user_id
     */
	public function addDigestSubscriber($user_id)
	{
		if ($this->digest_subscriber_model->where('user_id', $user_id)->count() == 0)
		{
			$this->digest_subscriber_model->create(['user_id' => $user_id]);
		}
	}

	/**
	 * @param $user_id
     */
	public function removeDigestSubscriber($user_id)
	{
		$subscription = $this->digest_subscriber_model->where('user_id', $user_id)->first();

		if ($subscription)
		{
			$subscription->delete();
		}
	}
}