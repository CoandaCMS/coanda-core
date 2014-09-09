<?php namespace CoandaCMS\Coanda\Users\Repositories\Eloquent;

use Coanda, Auth, Validator;

use CoandaCMS\Coanda\Exceptions\NotLoggedIn;
use CoandaCMS\Coanda\Exceptions\ValidationException;
use CoandaCMS\Coanda\Users\Exceptions\GroupNotFound;
use CoandaCMS\Coanda\Users\Exceptions\UserNotFound;
use CoandaCMS\Coanda\Users\Exceptions\AuthenticationFailed;
use CoandaCMS\Coanda\Exceptions\MissingInput;
use CoandaCMS\Coanda\Users\Repositories\Eloquent\Models\User as UserModel;
use CoandaCMS\Coanda\Users\Repositories\Eloquent\Models\UserGroup as UserGroupModel;
use CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface;

/**
 * Class EloquentUserRepository
 * @package CoandaCMS\Coanda\Users\Repositories\Eloquent
 */
class EloquentUserRepository implements UserRepositoryInterface {

    /**
     * @var Models\User
     */
    private $model;

    private $current_user_permissions;

    /**
     * @param UserModel $model
     */
    public function __construct(UserModel $model)
	{
		$this->model = $model;
	}

    /**
     * @return mixed
     */
    public function isLoggedIn()
	{
		return Auth::check();
	}

    /**
     * @return mixed
     * @throws NotLoggedIn
     */
    public function currentUser()
	{
		if (Auth::check())
		{
			return Auth::user();
		}
		
		throw new NotLoggedIn('Call to currentUser when user is not logged in.');
	}

    /**
     * @param string $username
     * @param string $password
     * @throws MissingInput
     * @throws AuthenticationFailed
     */
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

    /**
     * @return mixed
     */
    public function logout()
	{
		return Auth::logout();
	}

	public function currentUserPermissions()
	{
		if (!$this->current_user_permissions)
		{
			$user = Coanda::currentUser();
			$permissions = [];

			if ($user)
			{
				foreach ($user->groups as $group)
				{
					$permissions = array_merge($permissions, $group->access_list);
				}
			}

			$this->current_user_permissions = $permissions;
		}

		return $this->current_user_permissions;
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

    /**
     * @param $group_id
     * @return mixed
     * @throws \CoandaCMS\Coanda\Users\Exceptions\GroupNotFound
     */
    public function groupById($group_id)
	{
		$group = UserGroupModel::find($group_id);

		if ($group)
		{
			return $group;
		}

		throw new GroupNotFound;
	}

    /**
     * @return mixed
     */
    public function groups()
	{
		return UserGroupModel::get();
	}

	private function processPermissions($permissions)
	{
		if (array_key_exists('*', $permissions))
		{
			$permissions = ['*'];
		}

		$final_permissions = [];

		foreach ($permissions as $module => $module_permissions)
		{
			if (array_key_exists('allowed_paths', $module_permissions))
			{
				$allowed_paths = [];

				foreach ($module_permissions['allowed_paths'] as $allowed_path)
				{
					if ($allowed_path !== '')
					{
						$allowed_paths[] = $allowed_path;
					}
				}

				$allowed_paths = array_unique($allowed_paths);

				$module_permissions['allowed_paths'] = $allowed_paths;
			}

			$final_permissions[$module] = $module_permissions;
		}

		$permissions = $final_permissions;


		return $permissions;
	}

    /**
     * @param $data
     * @throws \CoandaCMS\Coanda\Exceptions\ValidationException
     */
    public function createGroup($data)
	{
		$invalid_fields = [];

		if (!isset($data['name']) || $data['name'] == '')
		{
			$invalid_fields['name'] = 'Please enter a name';
		}

		if (!isset($data['permissions']))
		{
			$invalid_fields['permissions'] = 'Please specify the permissions for this group';
		}

		if (count($invalid_fields) > 0)
		{
			throw new ValidationException($invalid_fields);
		}

		$permissions = $this->processPermissions($data['permissions']);

		$user_group = new UserGroupModel;
		$user_group->name = $data['name'];
		$user_group->permissions = json_encode($permissions);
		$user_group->save();

		return $user_group;
	}

    /**
     * @param $group_id
     * @param $data
     * @throws \CoandaCMS\Coanda\Exceptions\ValidationException
     * @throws \CoandaCMS\Coanda\Users\Exceptions\GroupNotFound
     */
    public function updateGroup($group_id, $data)
	{
		$group = UserGroupModel::find($group_id);

		if (!$group)
		{
			throw new GroupNotFound;
		}

		$invalid_fields = [];

		if (!isset($data['name']) || $data['name'] == '')
		{
			$invalid_fields['name'] = 'Please enter a name';
		}

		if (!isset($data['permissions']))
		{
			$invalid_fields['permissions'] = 'Please specify the permissions for this group';
		}

		if (count($invalid_fields) > 0)
		{
			throw new ValidationException($invalid_fields);
		}

		$permissions = $this->processPermissions($data['permissions']);

		$group->name = $data['name'];
		$group->permissions = json_encode($permissions);
		$group->save();
	}

    /**
     * @param $data
     * @param $group_id
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\ValidationException
     * @throws \CoandaCMS\Coanda\Users\Exceptions\GroupNotFound
     */
    public function createNew($data, $group_id)
	{
		$group = UserGroupModel::find($group_id);

		if (!$group)
		{
			throw new GroupNotFound;
		}

		$invalid_fields = [];

		$validation_rules = [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required|email|unique:users',
			'password' => 'required|confirmed'
		];

		$validator = Validator::make($data, $validation_rules);

		if ($validator->fails())
		{
			foreach ($validator->messages()->getMessages() as $field => $messages)
			{
				$invalid_fields[$field] = implode(', ', $messages);
			}

			throw new ValidationException($invalid_fields);
		}

		// Create the user model and attach it to the group, then return the user.
		$user = new $this->model;
		$user->first_name = $data['first_name'];
		$user->last_name = $data['last_name'];
		$user->email = $data['email'];
		$user->password = \Hash::make($data['password']);
		$user->save();

		$group->users()->attach($user->id);

		return $user;
	}

    /**
     * @param $user_id
     * @param $data
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\ValidationException
     * @throws \CoandaCMS\Coanda\Users\Exceptions\UserNotFound
     */
    public function updateExisting($user_id, $data)
	{		
		$user = $this->model->find($user_id);

		if (!$user)
		{
			throw new UserNotFound;
		}

		$invalid_fields = [];

		$validation_rules = [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required|email|unique:users,email,' . $user->id,
			'password' => 'confirmed',
		];

		$validator = Validator::make($data, $validation_rules);

		if ($validator->fails())
		{
			foreach ($validator->messages()->getMessages() as $field => $messages)
			{
				$invalid_fields[$field] = implode(', ', $messages);
			}

			throw new ValidationException($invalid_fields);
		}

		$user->first_name = $data['first_name'];
		$user->last_name = $data['last_name'];
		$user->email = $data['email'];

		if ($data['password'] && $data['password'] !== '')
		{
			$user->password = \Hash::make($data['password']);	
		}
		
		$user->save();

		return $user;
	}

    /**
     * @param $user_id
     * @param $group_id
     * @throws \CoandaCMS\Coanda\Users\Exceptions\GroupNotFound
     * @throws \CoandaCMS\Coanda\Users\Exceptions\UserNotFound
     */
    public function addUserToGroup($user_id, $group_id)
	{
		$user = $this->model->find($user_id);

		if (!$user)
		{
			throw new UserNotFound;
		}

		$group = UserGroupModel::find($group_id);

		if (!$group)
		{
			throw new GroupNotFound;
		}

		$existing_groups = $user->groups->lists('id');

		if (!in_array($group->id, $existing_groups))
		{
			$group->users()->attach($user->id);
		}
	}

    /**
     * @param $user_id
     * @param $group_id
     * @throws \CoandaCMS\Coanda\Users\Exceptions\GroupNotFound
     * @throws \CoandaCMS\Coanda\Users\Exceptions\UserNotFound
     */
    public function removeUserFromGroup($user_id, $group_id)
	{
		$user = $this->model->find($user_id);

		if (!$user)
		{
			throw new UserNotFound;
		}

		$group = UserGroupModel::find($group_id);

		if (!$group)
		{
			throw new GroupNotFound;
		}

		$group->users()->detach($user->id);
	}

    /**
     * @param $ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByIds($ids)
	{
		$users = new \Illuminate\Database\Eloquent\Collection;

		if (!is_array($ids))
		{
			return $users;
		}

		foreach ($ids as $id)
		{
			$user = $this->model->find($id);

			if ($user)
			{
				$users->add($user);
			}
		}

		return $users;
	}
}