<?php namespace CoandaCMS\Coanda\Controllers\Admin;

use View, App, Coanda, Redirect, Input, Session;

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

}