<?php namespace CoandaCMS\Coanda;

use Illuminate\Support\Facades\Config;

use CoandaCMS\Coanda\Authentication\User;

class Coanda {

	private $user;

	public function __construct()
	{
	}

	/**
	 * Takes the path and prepends the current admin_path from the config
	 * @param  string $path
	 * @return string
	 */
	public static function adminUrl($path)
	{
		return url(Config::get('coanda::coanda.admin_path') . '/' . $path);
	}

	/**
	 * Checks to see if we have a user
	 * @return boolean
	 */
	public static function isLoggedIn()
	{
		User::isLoggedIn();
	}

	/**
	 * Returns the current user
	 * @return boolean
	 */
	public static function currentUser()
	{
		User::currentUser();
	}

}