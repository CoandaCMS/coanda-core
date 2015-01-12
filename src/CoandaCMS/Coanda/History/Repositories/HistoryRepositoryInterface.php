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
     * @param $limit
     * @return mixed
     */
    public function get($for, $for_id, $limit);

    /**
     * @param $for
     * @param $for_id
     * @param $limit
     * @return mixed
     */
    public function getPaginated($for, $for_id, $limit);

    /**
     * @param $limit
     * @return mixed
     */
    public function getAllPaginated($limit);

    /**
     * @param $from
     * @param $to
     * @param $limit
     * @return mixed
     */
    public function getAllPaginatedByTimePeriod($from, $to, $limit);

    /**
     * @return mixed
     */
    public function getActivitySummaryFigures();

    /**
     * @return mixed
     */
    public function getDigestSummaryFigures();

    /**
     * @param $for
     * @param $for_id
     * @return mixed
     */
    public function users($for, $for_id);

    /**
     * @param $user_id
     * @return mixed
     */
    public function getDigestSubscriber($user_id);

    /**
     * @param $limit
     * @param $offset
     * @return mixed
     */
    public function getDigestSubscribers($limit, $offset);

    /**
     * @param $user_id
     * @return mixed
     */
    public function addDigestSubscriber($user_id);

    /**
     * @param $user_id
     * @return mixed
     */
    public function removeDigestSubscriber($user_id);
}
