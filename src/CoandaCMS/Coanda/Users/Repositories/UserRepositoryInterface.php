<?php namespace CoandaCMS\Coanda\Users\Repositories;

interface UserRepositoryInterface {

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

	public function hasAccessTo($permission, $permission_id = false);

	public function find($id);
	public function createNew($data, $group_id);
	public function updateExisting($user_id, $data);

	
	public function groupById($group_id);
	public function groups();
	public function createGroup($data);
	public function updateGroup($group_id, $data);

}
