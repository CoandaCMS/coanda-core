<?php namespace CoandaCMS\Coanda\Controllers\Admin;

use View, App, Coanda, Redirect, Input, Session;

use CoandaCMS\Coanda\Exceptions\ValidationException;
use CoandaCMS\Coanda\Users\Exceptions\GroupNotFound;
use CoandaCMS\Coanda\Users\Exceptions\UserNotFound;

use CoandaCMS\Coanda\Controllers\BaseController;

class UsersAdminController extends BaseController {

	private $userRepository;

	public function __construct(\CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface $userRepository)
	{
		$this->userRepository = $userRepository;

		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		$groups = $this->userRepository->groups();

		return View::make('coanda::admin.users.index', [ 'groups' => $groups ]);
	}

	public function getCreateGroup()
	{
		$permissions = Coanda::availablePermissions();
		$invalid_fields = Session::has('invalid_fields') ? Session::get('invalid_fields') : [];

		return View::make('coanda::admin.users.creategroup', ['permissions' => $permissions, 'invalid_fields' => $invalid_fields ]);
	}

	public function postCreateGroup()
	{
		if (Input::has('cancel'))
		{
			return Redirect::to(Coanda::adminUrl('users'));
		}

		try
		{
			$this->userRepository->createGroup(Input::all());

			return Redirect::to(Coanda::adminUrl('users'));
		}
		catch (ValidationException $exception)
		{
			return Redirect::to(Coanda::adminUrl('users/create-group'))->with('error', true)->with('invalid_fields', $exception->getInvalidFields())->withInput();
		}
	}

	public function getEditGroup($group_id)
	{
		try
		{
			$group = $this->userRepository->groupById($group_id);

			$permissions = Coanda::availablePermissions();
			$existing_permissions = Input::old('permissions', (array)$group->access_list);

			$invalid_fields = Session::has('invalid_fields') ? Session::get('invalid_fields') : [];

			return View::make('coanda::admin.users.editgroup', ['group' => $group, 'existing_permissions' => $existing_permissions, 'permissions' => $permissions, 'invalid_fields' => $invalid_fields ]);
		}
		catch (GroupNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('users'));
		}
	}

	public function postEditGroup($group_id)
	{
		try
		{
			if (Input::has('cancel'))
			{
				return Redirect::to(Coanda::adminUrl('users'));
			}

			$this->userRepository->updateGroup($group_id, Input::all());

			return Redirect::to(Coanda::adminUrl('users'));			
		}
		catch (GroupNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('users'));
		}
		catch (ValidationException $exception)
		{
			return Redirect::to(Coanda::adminUrl('users/edit-group/' . $group_id))->with('error', true)->with('invalid_fields', $exception->getInvalidFields())->withInput();
		}
	}

	public function getGroup($group_id)
	{
		try
		{
			Session::put('last_group_view', $group_id);

			$group = $this->userRepository->groupById($group_id);

			return View::make('coanda::admin.users.group', ['group' => $group ]);
		}
		catch (GroupNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('users'));
		}
	}

	public function getCreateUser($group_id)
	{
		try
		{
			$group = $this->userRepository->groupById($group_id);
			$invalid_fields = Session::has('invalid_fields') ? Session::get('invalid_fields') : [];

			return View::make('coanda::admin.users.createuser', ['group' => $group, 'invalid_fields' => $invalid_fields ]);
		}
		catch (GroupNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('users'));
		}
	}

	public function postCreateUser($group_id)
	{
		if (Input::has('cancel'))
		{
			return Redirect::to(Coanda::adminUrl('users/group/' . $group_id));
		}

		try
		{
			$this->userRepository->createNew(Input::all(), $group_id);

			return Redirect::to(Coanda::adminUrl('users/group/' . $group_id));
		}
		catch (ValidationException $exception)
		{
			return Redirect::to(Coanda::adminUrl('users/create-user/' . $group_id))->with('error', true)->with('invalid_fields', $exception->getInvalidFields())->withInput();
		}
		catch (GroupNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('users'));
		}
	}	

	public function getEditUser($user_id)
	{
		try
		{
			$user = $this->userRepository->find($user_id);

			$invalid_fields = Session::has('invalid_fields') ? Session::get('invalid_fields') : [];

			return View::make('coanda::admin.users.edituser', ['user' => $user, 'invalid_fields' => $invalid_fields ]);
		}
		catch (UserNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('users'));
		}
	}

	public function postEditUser($user_id)
	{
		$last_group_id = Session::get('last_group_view');

		if (Input::has('cancel'))
		{
			return Redirect::to(Coanda::adminUrl('users/group/' . $last_group_id));
		}

		try
		{
			$user = $this->userRepository->find($user_id);

			$this->userRepository->updateExisting($user_id, Input::all());

			return Redirect::to(Coanda::adminUrl('users/group/' . $last_group_id));
		}
		catch (ValidationException $exception)
		{
			return Redirect::to(Coanda::adminUrl('users/edit-user/' . $user_id))->with('error', true)->with('invalid_fields', $exception->getInvalidFields())->withInput();
		}
		catch (UserNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('users/group/' . $last_group_id));
		}
	}

	public function getUser($user_id, $selected_tab = '')
	{
		$last_group_id = Session::get('last_group_view');

		try
		{
			$user = $this->userRepository->find($user_id);

			return View::make('coanda::admin.users.user', ['user' => $user, 'selected_tab' => $selected_tab ]);
		}
		catch (UserNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('users/group/' . $last_group_id));
		}
	}

	public function getAddToGroup($user_id, $group_id)
	{
		try
		{
			$this->userRepository->addUserToGroup($user_id, $group_id);

			return Redirect::to(Coanda::adminUrl('users/user/' . $user_id . '/groups'));
		}
		catch (UserNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('users'));
		}
		catch (GroupNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('users'));
		}
	}

	public function getRemoveFromGroup($user_id, $group_id)
	{
		try
		{
			$this->userRepository->removeUserFromGroup($user_id, $group_id);

			return Redirect::to(Coanda::adminUrl('users/user/' . $user_id . '/groups'));
		}
		catch (UserNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('users'));
		}
		catch (GroupNotFound $exception)
		{
			return Redirect::to(Coanda::adminUrl('users'));
		}
	}	
}