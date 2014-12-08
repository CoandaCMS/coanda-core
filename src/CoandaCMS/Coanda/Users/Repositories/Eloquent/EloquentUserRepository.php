<?php namespace CoandaCMS\Coanda\Users\Repositories\Eloquent;

use CoandaCMS\Coanda\Exceptions\ValidationException;
use CoandaCMS\Coanda\Users\Exceptions\GroupNotFound;
use CoandaCMS\Coanda\Users\Exceptions\UserNotFound;
use CoandaCMS\Coanda\Users\Repositories\Eloquent\Models\User;
use CoandaCMS\Coanda\Users\Repositories\Eloquent\Models\UserGroup;
use CoandaCMS\Coanda\Users\Repositories\Eloquent\Models\ArchivedUser;
use CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface;
use Illuminate\Hashing\HasherInterface;

class EloquentUserRepository implements UserRepositoryInterface {


	/**
	 * @var User
     */
	private $user_model;

	/**
	 * @var UserGroup
     */
	private $user_group_model;

	/**
	 * @var ArchivedUser
     */
	private $archived_user_model;

    /**
     * @var HasherInterface
     */
    private $hasher;

	/**
	 * @param User $user_model
	 * @param UserGroup $user_group_model
	 * @param ArchivedUser $archived_user_model
	 * @param HasherInterface $hasher
     */
	public function __construct(User $user_model, UserGroup $user_group_model, ArchivedUser $archived_user_model, HasherInterface $hasher)
	{
		$this->user_model = $user_model;
        $this->user_group_model = $user_group_model;
		$this->archived_user_model = $archived_user_model;
        $this->hasher = $hasher;
	}

    /**
     * @param $id
     * @return mixed
     * @throws UserNotFound
     */
    public function find($id)
	{
		$user = $this->user_model->find($id);

		if (!$user)
		{
			throw new UserNotFound('User #' . $id . ' not found');
		}
		
		return $user;
	}

	/**
	 * @param $id
	 * @return mixed
     */
	public function findArchivedUser($id)
	{
		return $this->archived_user_model->whereUserId($id)->first();
	}

    /**
     * @param $email
     * @return mixed
     * @throws UserNotFound
     */
    public function findByEmail($email)
    {
        $user = $this->user_model->where('email', '=', $email)->first();

        if (!$user)
        {
            throw new UserNotFound('User with email: ' . $email . ' not found');
        }

        return $user;
    }

    /**
     * @param $group_id
     * @return mixed
     * @throws GroupNotFound
     */
    public function groupById($group_id)
	{
		$group = $this->user_group_model->find($group_id);

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
		return $this->user_group_model->get();
	}

    /**
     * @param $permissions
     * @return array
     */
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
     * @return mixed
     * @throws ValidationException
     */
    public function createGroup($data)
	{
		$permissions = $this->processPermissions($data['permissions']);

		$user_group = new $this->user_group_model;
		$user_group->name = $data['name'];
		$user_group->permissions = json_encode($permissions);
		$user_group->save();

		return $user_group;
	}

    /**
     * @param $group
     * @param $data
     * @return mixed|void
     * @throws GroupNotFound
     * @throws ValidationException
     */
    public function updateGroup($group, $data)
	{
		$permissions = $this->processPermissions($data['permissions']);

		$group->name = $data['name'];
		$group->permissions = json_encode($permissions);
		$group->save();
	}

    /**
     * @param $data
     * @param $group
     * @return mixed
     * @throws GroupNotFound
     * @throws ValidationException
     */
    public function createNewUser($data, $group)
	{
		// Create the user model and attach it to the group, then return the user.
		$user = new $this->user_model;
		$user->first_name = $data['first_name'];
		$user->last_name = $data['last_name'];
		$user->email = $data['email'];
		$user->password = $this->hasher->make($data['password']);
		$user->save();

		$group->users()->attach($user->id);

		return $user;
	}

    /**
     * @param $user
     * @param $data
     * @return mixed
     * @throws UserNotFound
     * @throws ValidationException
     */
    public function updateExistingUser($user, $data)
	{		
		$user->first_name = $data['first_name'];
		$user->last_name = $data['last_name'];
		$user->email = $data['email'];

		if ($data['password'] && $data['password'] !== '')
		{
			$user->password = $this->hasher->make($data['password']);
		}
		
		$user->save();

		return $user;
	}

    /**
     * @param $user
     * @param $group
     * @return mixed|void
     * @throws GroupNotFound
     * @throws UserNotFound
     */
    public function addUserToGroup($user, $group)
	{
		$existing_groups = $user->groups->lists('id');

		if (!in_array($group->id, $existing_groups))
		{
			$group->users()->attach($user->id);
		}
	}

    /**
     * @param $user
     * @param $group
     * @return mixed|void
     * @throws GroupNotFound
     * @throws UserNotFound
     */
    public function removeUserFromGroup($user, $group)
	{
		$group->users()->detach($user->id);
	}

	/**
	 * @param $user
	 * @return mixed
     */
	public function createArchivedUserAccount($user)
	{
		return $this->archived_user_model->create([
			'user_id' => $user->id,
			'name' => $user->present()->name,
			'email' => $user->email
		]);
	}

}