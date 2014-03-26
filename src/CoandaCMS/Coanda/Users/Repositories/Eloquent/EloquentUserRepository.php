<?php namespace CoandaCMS\Coanda\Users\Repositories\Eloquent;

use Coanda, Auth;

use CoandaCMS\Coanda\Users\Repositories\Eloquent\Models\User as UserModel;

use CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface;

class EloquentUserRepository implements UserRepositoryInterface {

	private $model;

	public function __construct(UserModel $model)
	{
		$this->model = $model;
	}

	public function isLoggedIn()
	{
		return Auth::check();
	}
	
	public function currentUser()
	{
		if (Auth::check())
		{
			return Auth::user();
		}
		
		throw new NotLoggedIn('Call to currentUser when user is not logged in.');
	}

	public function login($username, $password)
	{		
		$missing_fields = [];

		if (!$username || $username === '')
		{
			$missing_fields[] = 'username';
		}

		if (!$password || $password === '')
		{
			$missing_fields[] = 'password';
		}

		if (count($missing_fields) > 0)
		{
			throw new MissingInput($missing_fields);
		}

		if (!Auth::attempt(array('email' => $username, 'password' => $password)))
		{
		    throw new AuthenticationFailed;
		}
	}

	public function logout()
	{
		return Auth::logout();
	}

	/**
	 * Tries to find the Eloquent page model by the id
	 * @param  integer $id
	 * @return Array
	 */
	public function find($id)
	{
		$user = $this->model->find($id);

		if (!$user)
		{
			throw new UserNotFound('User #' . $id . ' not found');
		}
		
		return $user;
	}

	public function create()
	{
		dd('create user');
	}
}