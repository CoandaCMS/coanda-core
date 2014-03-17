<?php namespace CoandaCMS\Coanda\Controllers;

use Input, View, Redirect, Lang;

use Coanda;
use CoandaCMS\Coanda\Authentication\User;

// Exceptions
use CoandaCMS\Coanda\Exceptions\MissingInput;
use CoandaCMS\Coanda\Exceptions\AuthenticationFailed;

class Admin extends Base {

	public function __construct()
	{
		$this->beforeFilter('admin_auth', array('except' => array('getLogin', 'postLogin')));
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		return 'Admin site, home page';
	}

	public function getLogin()
	{
		return View::make('coanda::admin.login');
	}

	public function postLogin()
	{
		try
		{
			User::login(Input::get('email'), Input::get('password'));
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
		User::logout();

		return Redirect::to(Coanda::adminUrl('/'));
	}

}