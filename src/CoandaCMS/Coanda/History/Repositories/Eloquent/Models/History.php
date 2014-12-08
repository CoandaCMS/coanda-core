<?php namespace CoandaCMS\Coanda\History\Repositories\Eloquent\Models;

use CoandaCMS\Coanda\Users\Exceptions\UserNotFound;
use Eloquent, Coanda, App;

/**
 * Class History
 * @package CoandaCMS\Coanda\History\Repositories\Eloquent\Models
 */
class History extends Eloquent {

	use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = 'CoandaCMS\Coanda\History\Presenters\History';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'history';

    /**
     * @return mixed
     */
    public function user()
	{
		$userRepository = \App::make('CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface');

		try
		{
			$user = $userRepository->find($this->user_id);
		}
		catch (UserNotFound $exception)
		{
			$user = $userRepository->findArchivedUser($this->user_id);
		}

		return $user;
	}

    /**
     * @return mixed
     */
    public function getUserAttribute()
	{
		return $this->user();
	}

    /**
     * @return mixed
     */
    public function actionData()
	{
		$array = json_decode($this->data, true);
		
		return !$array ? $this->data : $array;
	}

    /**
     * @return mixed
     */
    public function getActionDataAttribute()
	{
		return $this->actionData();
	}
}