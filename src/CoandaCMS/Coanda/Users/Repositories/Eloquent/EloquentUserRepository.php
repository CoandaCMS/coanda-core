<?php namespace CoandaCMS\Coanda\Users\Repositories\Eloquent;

use Coanda, Auth;

use CoandaCMS\Coanda\Exceptions\ValidationException;
use CoandaCMS\Coanda\Users\Exceptions\GroupNotFound;

use CoandaCMS\Coanda\Users\Repositories\Eloquent\Models\User as UserModel;
use CoandaCMS\Coanda\Users\Repositories\Eloquent\Models\UserGroup as UserGroupModel;

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

	public function hasAccessTo($permission, $permission_id = false)
	{
		// Get all the groups for the user
		$user = Coanda::currentUser();

		// Try to split up into the module:view
		$permission_parts = explode(':', $permission);

		if (count($permission_parts) == 2)
		{
			$requested_module = $permission_parts[0];
			$requested_view = $permission_parts[1];
		}
		else
		{
			// If we are only requesting a module, e.g. They can do something in the module!
			$requested_module = $permission;
			$requested_view = false;
		}

		// Loop through each of the user groups
		foreach ($user->groups as $group)
		{
			// Get the access list for the group
			$access_list = $group->access_list;

			foreach ($access_list as $module => $access)
			{
				// This is a wilcard, they can see anything with this.
				if ($access == '*')
				{
					return true;
				}

				// If the module is the requested one
				if ($module == $requested_module)
				{
					// If we have not requested a specific view, but we have the module, then OK.
					if (!$requested_view)
					{
						return true;
					}

					// Check for the specific view in the access list
					if (in_array($requested_view, $access))
					{
						return true;
					}
				}
			}
		}

		return false;
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

	public function groupById($group_id)
	{
		$group = UserGroupModel::find($group_id);

		if ($group)
		{
			return $group;
		}

		throw new GroupNotFound;
	}

	public function groups()
	{
		return UserGroupModel::get();
	}

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

		$permissions = $data['permissions'];

		if (array_key_exists('*', $permissions))
		{
			$permissions = ['*'];
		}

		$user_group = new UserGroupModel;
		$user_group->name = $data['name'];
		$user_group->permissions = json_encode($permissions);
		$user_group->save();
	}

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

		$permissions = $data['permissions'];

		if (array_key_exists('*', $permissions))
		{
			$permissions = ['*'];
		}

		$group->name = $data['name'];
		$group->permissions = json_encode($permissions);
		$group->save();
	}	
}