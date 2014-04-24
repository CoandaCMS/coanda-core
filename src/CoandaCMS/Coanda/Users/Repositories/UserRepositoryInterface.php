<?php namespace CoandaCMS\Coanda\Users\Repositories;

/**
 * Interface UserRepositoryInterface
 * @package CoandaCMS\Coanda\Users\Repositories
 */
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

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param $data
     * @param $group_id
     * @return mixed
     */
    public function createNew($data, $group_id);

    /**
     * @param $user_id
     * @param $data
     * @return mixed
     */
    public function updateExisting($user_id, $data);

    /**
     * @param $user_id
     * @param $grouo_id
     * @return mixed
     */
    public function addUserToGroup($user_id, $grouo_id);

    /**
     * @param $user_id
     * @param $grouo_id
     * @return mixed
     */
    public function removeUserFromGroup($user_id, $grouo_id);

    /**
     * @param $group_id
     * @return mixed
     */
    public function groupById($group_id);

    /**
     * @return mixed
     */
    public function groups();

    /**
     * @param $data
     * @return mixed
     */
    public function createGroup($data);

    /**
     * @param $group_id
     * @param $data
     * @return mixed
     */
    public function updateGroup($group_id, $data);

    /**
     * @param $ids
     * @return mixed
     */
    public function getByIds($ids);
    
}
