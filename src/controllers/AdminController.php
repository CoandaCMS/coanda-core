<?php namespace CoandaCMS\Coanda\Controllers;

use CoandaCMS\Coanda\Users\UserManager;
use Input, View, Redirect, Lang, Session;

use Coanda;

// Exceptions
use CoandaCMS\Coanda\Exceptions\MissingInput;
use CoandaCMS\Coanda\Users\Exceptions\AuthenticationFailed;

/**
 * Class AdminController
 * @package CoandaCMS\Coanda\Controllers
 */
class AdminController extends BaseController {

    private $user;

    /**
     * @param UserManager $user
     */
    public function __construct(UserManager $user)
	{
		$this->user = $user;

		$this->beforeFilter('admin_auth', array('except' => array('getLogin', 'postLogin')));
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

    /**
     * @return mixed
     */
    public function getIndex()
	{
		return View::make('coanda::admin.home');
	}

    /**
     * @return mixed
     */
    public function getLogin()
	{
		// If the user is logged in, they don't need to see this
		if ($this->user->isLoggedIn())
		{
			return Redirect::to(Coanda::adminUrl('/'));
		}

		return View::make('coanda::admin.login');
	}

    /**
     * @return mixed
     */
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
				$redirect_path = Session::pull('pre_auth_path');

				if (Session::has('pre_auth_query_string'))
				{
					$redirect_path .= '?' . Session::pull('pre_auth_query_string');
				}

				return Redirect::to($redirect_path);
			}

			return Redirect::to(Coanda::adminUrl('/'));
		}
		catch (MissingInput $exception)
		{
			$errors = new \Illuminate\Support\MessageBag;

			foreach ($exception->getMissingFields() as $missing_field)
			{
				$errors->add($missing_field, Lang::get('coanda::errors.missing_' . $missing_field));
			}

			return Redirect::to(Coanda::adminUrl('login'))->withInput()->withErrors($errors);
		}
		catch (AuthenticationFailed $exception)
		{
			$errors = new \Illuminate\Support\MessageBag;

			$errors->add('username', Lang::get('coanda::errors.invalid_username'));

			return Redirect::to(Coanda::adminUrl('login'))->withInput()->withErrors($errors);
		}
	}

    /**
     * @return mixed
     */
    public function getLogout()
	{
		$this->user->logout();

		return Redirect::to(Coanda::adminUrl('/'));
	}

    /**
     * @return mixed
     */
    public function getSearch()
	{
		$query = Input::has('q') ? Input::get('q') : false;

		$results = Coanda::handleAdminSearch($query);

		return View::make('coanda::admin.search', ['results' => $results, 'query' => $query]);
	}

}