<?php namespace CoandaCMS\Coanda\Users\Repositories;

interface UserRepositoryInterface {

    /**
     * @return mixed
     */
    public function isLoggedIn();

    /**
     * @return mixed
     */
    public function currentUser();

    /**
     * @param $username
     * @param $password
     * @return mixed
     */
    public function login($username, $password);

    /**
     * @return mixed
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
     * @param $group_id
     * @return mixed
     */
    public function addUserToGroup($user_id, $group_id);

    /**
     * @param $user_id
     * @param $group_id
     * @return mixed
     */
    public function removeUserFromGroup($user_id, $group_id);

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
