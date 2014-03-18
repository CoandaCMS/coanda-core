<?php namespace CoandaCMS\Coanda;

use Illuminate\Support\Facades\Config;

class Coanda {

	/**
	 * Our user implementation
	 * @var [type]
	 */
	private $user;

	/**
	 * @param CoandaCMSCoandaAuthenticationUser $user [description]
	 */
	public function __construct(\CoandaCMS\Coanda\Authentication\User $user)
	{
		$this->user = $user;
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
	public function isLoggedIn()
	{
		return $this->user->isLoggedIn();
	}

	/**
	 * Returns the current user
	 * @return boolean
	 */
	public function currentUser()
	{
		return $this->user->currentUser();
	}

}