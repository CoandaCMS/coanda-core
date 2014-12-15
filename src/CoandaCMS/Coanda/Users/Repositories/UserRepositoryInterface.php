<?php namespace CoandaCMS\Coanda\Users\Repositories;

interface UserRepositoryInterface {

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @return mixed
     */
    public function totalUserCount();

    /**
     * @param $id
     * @return mixed
     */
    public function findArchivedUser($id);

    /**
     * @param $email
     * @return mixed
     */
    public function findByEmail($email);

    /**
     * @param $data
     * @param $group_id
     * @return mixed
     */
    public function createNewUser($data, $group_id);

    /**
     * @param $user
     * @param $data
     * @return mixed
     */
    public function updateExistingUser($user, $data);

    /**
     * @param $user
     * @param $group
     * @return mixed
     */
    public function addUserToGroup($user, $group);

    /**
     * @param $user
     * @param $group
     * @return mixed
     */
    public function removeUserFromGroup($user, $group);

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
     * @param $current_user_id
     * @return mixed
     */
    public function getCurrentlyOnlineUsers($current_user_id);
}
