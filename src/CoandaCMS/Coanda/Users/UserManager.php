<?php namespace CoandaCMS\Coanda\Users;

use CoandaCMS\Coanda\Exceptions\MissingInput;
use CoandaCMS\Coanda\Exceptions\NotLoggedIn;
use CoandaCMS\Coanda\Users\Exceptions\AuthenticationFailed;
use CoandaCMS\Coanda\Users\Exceptions\UserNotFound;
use CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface;
use Illuminate\Auth\AuthManager;
use CoandaCMS\Coanda\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Factory as ValidationFactory;

class UserManager {

    /**
     * @var UserRepositoryInterface
     */
    private $repository;
    /**
     * @var AuthManager
     */
    private $auth;

    /**
     * @var ValidationFactory
     */
    private $validator;
    /**
     * @var
     */
    private $current_user_permissions;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param AuthManager $auth
     * @param ValidationFactory $validator
     */
    public function __construct(UserRepositoryInterface $userRepository, AuthManager $auth, ValidationFactory $validator)
    {
        $this->repository = $userRepository;
        $this->auth = $auth;
        $this->validator = $validator;
    }

    /**
     * @param $username
     * @param $password
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

        if (!$this->auth->attempt(array('email' => $username, 'password' => $password)))
        {
            throw new AuthenticationFailed;
        }
    }

    /**
     * @return mixed
     */
    public function logout()
    {
        return $this->auth->logout();
    }

    /**
     * @return mixed
     */
    public function isLoggedIn()
    {
        return $this->auth->check();
    }

    /**
     * @throws NotLoggedIn
     */
    public function currentUser()
    {
        if ($this->isLoggedIn())
        {
            return $this->auth->user();
        }

        throw new NotLoggedIn('Call to currentUser when user is not logged in.');
    }

    /**
     * @return array
     * @throws NotLoggedIn
     */
    public function currentUserPermissions()
    {
        if (!$this->current_user_permissions)
        {
            $user = $this->currentUser();
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
     * @return mixed
     */
    public function getAllGroups()
    {
        return $this->repository->groups();
    }

    /**
     * @param $group_id
     * @return mixed
     */
    public function getGroupById($group_id)
    {
        return $this->repository->groupById($group_id);
    }

    /**
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    public function createGroup($data)
    {
        $this->validateUserGroupData($data);

        return $this->repository->createGroup($data);
    }

    /**
     * @param $group_id
     * @param $data
     * @throws GroupNotFound
     * @throws ValidationException
     */
    public function updateGroup($group_id, $data)
    {
        $group = $this->getGroupById($group_id);

        if (!$group)
        {
            throw new GroupNotFound;
        }

        $this->validateUserGroupData($data);

        $this->repository->updateGroup($group, $data);
    }

    /**
     * @param $data
     * @throws ValidationException
     */
    private function validateUserGroupData($data)
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
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function getUserById($user_id)
    {
        return $this->repository->find($user_id);
    }

    /**
     * @param $email
     * @return mixed
     */
    public function getUserByEmail($email)
    {
        try
        {
            return $this->repository->findByEmail($email);
        }
        catch (UserNotFound $exception)
        {
            return false;
        }
    }

    /**
     * @param $data
     * @param $group_id
     * @return mixed
     * @throws GroupNotFound
     * @throws ValidationException
     */
    public function createNewUser($data, $group_id)
    {
        $group = $this->getGroupById($group_id);

        if (!$group)
        {
            throw new GroupNotFound;
        }

        $this->validateUserData($data);

        return $this->repository->createNewUser($data, $group);
    }

    /**
     * @param $user_id
     * @param $data
     * @throws UserNotFound
     * @throws ValidationException
     */
    public function updateExistingUser($user_id, $data)
    {
        $user = $this->getUserById($user_id);

        if (!$user)
        {
            throw new UserNotFound;
        }

        $this->validateUserData($data, $user->id);

        $this->repository->updateExistingUser($user, $data);
    }

    /**
     * @param $data
     * @param bool $existing_user_id
     * @throws ValidationException
     */
    private function validateUserData($data, $existing_user_id = false)
    {
        $invalid_fields = [];

        $validation_rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'confirmed',
        ];

        if ($existing_user_id)
        {
            $validation_rules['email'] = 'required|email|unique:users,email,' . $existing_user_id;
        }
        else
        {
            $validation_rules['email'] = 'required|email|unique:users';
        }

        $validator = $this->validator->make($data, $validation_rules);

        if ($validator->fails())
        {
            foreach ($validator->messages()->getMessages() as $field => $messages)
            {
                $invalid_fields[$field] = implode(', ', $messages);
            }

            throw new ValidationException($invalid_fields);
        }
    }

    /**
     * @param $user_id
     * @param $group_id
     * @throws GroupNotFound
     * @throws UserNotFound
     */
    public function addUserToGroup($user_id, $group_id)
    {
        list($user, $group) = $this->getUserAndGroup($user_id, $group_id);

        $this->repository->addUserToGroup($user, $group);
    }

    /**
     * @param $user_id
     * @param $group_id
     * @throws GroupNotFound
     * @throws UserNotFound
     */
    public function removeUserFromGroup($user_id, $group_id)
    {
        list($user, $group) = $this->getUserAndGroup($user_id, $group_id);

        $this->repository->removeUserFromGroup($user, $group);
    }

    /**
     * @param $ids
     * @return Collection
     */
    public function getByIds($ids)
    {
        $users = new Collection;

        if (!is_array($ids))
        {
            return $users;
        }

        foreach ($ids as $id)
        {
            try
            {
                $user = $this->getUserById($id);

                if ($user)
                {
                    $users->add($user);
                }
            }
            catch (UserNotFound $exception)
            {
                $archived_user = $this->repository->findArchivedUser($id);

                if ($archived_user)
                {
                    $users->add($archived_user);
                }
            }
        }

        return $users;
    }

    /**
     * @param $user_id
     * @param $group_id
     * @return array
     * @throws GroupNotFound
     * @throws UserNotFound
     */
    private function getUserAndGroup($user_id, $group_id)
    {
        $user = $this->getUserById($user_id);

        if (!$user)
        {
            throw new UserNotFound;
        }

        $group = $this->getGroupById($group_id);

        if (!$group)
        {
            throw new GroupNotFound;
        }

        return [$user, $group];
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function removeUserById($user_id)
    {
        $user = $this->repository->find($user_id);
        $groups = $user->groups()->get();

        foreach ($groups as $group)
        {
            $this->repository->removeUserFromGroup($user, $group);
        }

        $this->repository->createArchivedUserAccount($user);
        $user->delete();
    }

    /**
     * @param $group_id
     * @return mixed
     */
    public function removeGroupById($group_id)
    {
        $group = $this->repository->groupById($group_id);

        $users = $group->users()->get();

        foreach ($users as $user)
        {
            $this->repository->removeUserFromGroup($user, $group);

            $remaining_groups = $user->groups()->get();

            if ($remaining_groups->count() == 0)
            {
                $this->removeUserById($user->id);
            }
        }

        $group->delete();
    }
}
