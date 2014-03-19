<?php namespace CoandaCMS\Coanda\Authentication;

interface UserInterface {

	/**
	 * Check to see if we have a logged in user
	 * @return boolean
	 */
	public function isLoggedIn();

	/**
	 * Returns the current user
	 * @return ??
	 */
	public function currentUser();

	/**
	 * Attempts to login a user
	 * @param  string $username
	 * @param  string $password 
	 * @return mixed
	 */
	public function login($username, $password);

	/**
	 * Logs out the current user
	 * @return [type] [description]
	 */
	public function logout();

}
