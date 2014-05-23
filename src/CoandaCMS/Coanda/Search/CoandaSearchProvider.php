<?php namespace CoandaCMS\Coanda\Search;

/**
 * Interface CoandaSearchProvider
 * @package CoandaCMS\Coanda\Search
 */
interface CoandaSearchProvider {

    /**
     * @param $module
     * @param $module_id
     * @param $url
     * @param $search_data
     * @return mixed
     */
    public function register($module, $module_id, $url, $search_data);

    /**
     * @param $module
     * @param $module_id
     * @return mixed
     */
    public function unRegister($module, $module_id);

    /**
     * @return mixed
     */
    public function handleSearch();

}