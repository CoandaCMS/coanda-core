<?php namespace CoandaCMS\Coanda\Controllers;

use Input, View, Redirect, Lang, Session;

use Coanda;

// Exceptions
use CoandaCMS\Coanda\Exceptions\MissingInput;
use CoandaCMS\Coanda\Exceptions\AuthenticationFailed;

class AdminController extends BaseController {

	private $user;

	public function __construct(\CoandaCMS\Coanda\Authentication\UserInterface $user)
	{
		$this->user = $user;

		$this->beforeFilter('admin_auth', array('except' => array('getLogin', 'postLogin')));
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		return View::make('coanda::admin.home');
	}

	public function getLogin()
	{
		// If the user is logged in, they don't need to see this
		if ($this->user->isLoggedIn())
		{
			return Redirect::to(Coanda::adminUrl('/'));
		}

		return View::make('coanda::admin.login');
	}

	public function postLogin()
	{
		// If the user is logged in, they don't need to see this
		if ($this->user->isLoggedIn())
		{
			return Redirect::to(Coanda::adminUrl('/'));
		}
		
		try
		{
			$this->user->login(Input::get('email'), Input::get('password'));

			if (Session::has('pre_auth_path'))
			{
				$redirect_path = Session::get('pre_auth_path');

				Session::forget('pre_auth_path');

				return Redirect::to($redirect_path);
			}

			return Redirect::to(Coanda::adminUrl('/'));
		}
		catch(MissingInput $exception)
		{
			$errors = new \Illuminate\Support\MessageBag;

			foreach ($exception->getMissingFields() as $missing_field)
			{
				$errors->add($missing_field, Lang::get('coanda::errors.missing_' . $missing_field));
			}

			return Redirect::to(Coanda::adminUrl('login'))->withInput()->withErrors($errors);
		}
		catch(AuthenticationFailed $exception)
		{
			$errors = new \Illuminate\Support\MessageBag;

			$errors->add('username', Lang::get('coanda::errors.invalid_username'));

			return Redirect::to(Coanda::adminUrl('login'))->withInput()->withErrors($errors);
		}
	}

	public function getLogout()
	{
		$this->user->logout();

		return Redirect::to(Coanda::adminUrl('/'));
	}

}