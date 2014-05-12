<?php namespace CoandaCMS\Coanda\Urls\Repositories;

/**
 * Interface UrlRepositoryInterface
 * @package CoandaCMS\Coanda\Urls\Repositories
 */
interface UrlRepositoryInterface {

    /**
     * @param $for
     * @param $for_id
     * @return mixed
     */
    public function findFor($for, $for_id);

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id);

    /**
     * @param $slug
     * @return mixed
     */
    public function findBySlug($slug);

    /**
     * @param $slug
     * @param $for
     * @param $for_id
     * @return mixed
     */
    public function register($slug, $for, $for_id);

    /**
     * @param $for
     * @param $for_id
     * @return mixed
     */
    public function delete($for, $for_id);

    /**
     * @param $slug
     * @param $for
     * @param $for_id
     * @return mixed
     */
    public function canUse($slug, $for, $for_id);

    /**
     * @param $per_page
     * @return mixed
     */
    public function getList($per_page);

    /**
     * @param $type
     * @param $per_page
     * @return mixed
     */
    public function getListByType($type, $per_page);
    
}
