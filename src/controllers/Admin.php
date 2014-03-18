<?php namespace CoandaCMS\Coanda\Controllers;

use Input, View, Redirect, Lang;

use Coanda;

// Exceptions
use CoandaCMS\Coanda\Exceptions\MissingInput;
use CoandaCMS\Coanda\Exceptions\AuthenticationFailed;

class Admin extends Base {

	private $user;

	public function __construct(\CoandaCMS\Coanda\Authentication\User $user)
	{
		$this->user = $user;

		$this->beforeFilter('admin_auth', array('except' => array('getLogin', 'postLogin')));
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	public function getIndex()
	{
		$page = Page::getById(1);

		dd($page);

		return View::make('coanda::admin.home');
	}

	public function getLogin()
	{
		return View::make('coanda::admin.login');
	}

	public function postLogin()
	{
		try
		{
			$this->user->login(Input::get('email'), Input::get('password'));

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