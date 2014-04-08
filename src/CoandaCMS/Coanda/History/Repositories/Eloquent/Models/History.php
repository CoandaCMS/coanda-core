<?php namespace CoandaCMS\Coanda\History\Repositories\Eloquent\Models;

use Eloquent, Coanda, App;

class History extends Eloquent {

	use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

	protected $presenter = 'CoandaCMS\Coanda\History\Presenters\History';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'history';

	public function user()
	{
		$userRepository = \App::make('CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface');

		return $userRepository->find($this->user_id);
	}

	public function getUserAttribute()
	{
		return $this->user();
	}

	public function actionData()
	{
		$array = json_decode($this->data);
		
		return !$array ? $this->data : $array;
	}

	public function getActionDataAttribute()
	{
		return $this->actionData();
	}
}