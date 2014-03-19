<?php namespace CoandaCMS\Coanda\Authentication\Eloquent;

use Auth;

use CoandaCMS\Coanda\Exceptions\MissingInput;
use CoandaCMS\Coanda\Exceptions\AuthenticationFailed;
use CoandaCMS\Coanda\Exceptions\NotLoggedIn;

use CoandaCMS\Coanda\Authentication\UserInterface;

class EloquentUser implements UserInterface {

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
}
