<?php namespace CoandaCMS\Coanda\History\Repositories;

/**
 * Interface HistoryRepositoryInterface
 * @package CoandaCMS\Coanda\History\Repositories
 */
interface HistoryRepositoryInterface {

    /**
     * @param $for
     * @param $for_id
     * @param $user_id
     * @param $action
     * @param $data
     * @return mixed
     */
    public function add($for, $for_id, $user_id, $action, $data);

    /**
     * @param $for
     * @param $for_id
     * @return mixed
     */
    public function get($for, $for_id);

}
